<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'functions.php';
include_once 'db.php';
$x = $db->getChannels();
flog(json_encode($x));
flog('connecting to Twitch!!!');

$server = 'irc.chat.twitch.tv';
$port = 6667;
$timeout = 10;
$username = 'exhiled42';
$pw = 'oauth:plf1ojwv6jpwerudo40gi3cqf929iu';

function connectToTwitch($server, $port, $timeout, $username, $pw) {
    global $db;
    $socket = fsockopen($server, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        return false;
    }
    fwrite($socket, "CAP REQ :twitch.tv/membership twitch.tv/commands twitch.tv/tags\r\n");
    fwrite($socket, "PASS $pw\r\n");
    fwrite($socket, "NICK $username\r\n");
    
    
     $channels = $db->getChannels();
     flog('got channels? ' . $channels);
    foreach ($channels  as $channel) {
        $channelName = $channel['channel'];
        $x = "JOIN #$channelName\r\n";
        // sleep(1);
        flog($x);
        fwrite($socket, $x);
        flog('AUTOJOIN: '.$x);
    }

    flog('connected to Twitch as ' . $username);
    return $socket;
}
flog('about to do it');
$socket = connectToTwitch($server, $port, $timeout, $username, $pw);

while (true) {
    if (!$socket) {
        $socket = connectToTwitch($server, $port, $timeout, $username, $pw);
        flog('reconnected to Twitch');
        sleep(5);
        continue;
    }

    // IPC SHIT
    // flog('ipc starting');
    // $ipcHost = '127.0.0.1';
    // $ipcPort = 3000;
    // $ipc = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    // while (socket_bind($ipc, $ipcHost, $ipcPort) === false) {
    //     usleep(100000); // Delay for 100 milliseconds for times when the port is bound from a previous instance
    // }
    // socket_listen($ipc);
    // flog('ipc socket listening');
    // $clientSocket = socket_accept($ipc);
    // $command = socket_read($clientSocket, 1024);
    // socket_close($clientSocket);
    // if ($command) {
    //     $command = trim($command);
    //     if ($socket) {
    //         fwrite($socket, $command . "\r\n");
    //     }
    // }
      // END IPC SHIT

    while (!feof($socket)) {
        //flog('connected');
        $response = fgets($socket);
        if (strpos($response, 'PING') === 0) {
            fwrite($socket, 'PONG ' . substr($response, 5) . "\r\n");
            echo "PING PONG";
        }
        //flog($response);

        $re = '/@(.+)\.tmi\.twitch\.tv JOIN #(.+)/m';
        preg_match_all($re, $response, $matches, PREG_SET_ORDER, 0);
        if(!empty($matches)){
            $usr = $matches[0][1];
            $chan = $matches[0][2];
            $db->addJoin($usr,$chan);
        }


        $username='';$message='';$channel='';
        $pattern = '/:.*?!(.*?)@.*? PRIVMSG #(.*?) :(.*)/';
        preg_match($pattern, $response, $matches);
        if (count($matches) === 4) {
            $username = $matches[1];
            $channel = $matches[2];
            $message = $matches[3];
            echo "Username: $username" . PHP_EOL;
            echo "Channel: $channel" . PHP_EOL;
            echo "Message: $message" . PHP_EOL;
            $x = $db->addChat($username, $channel,$message,'{}');
            flog('add chat result ' + $x);
            flog(username.': '.$message);
        } else {
            //echo $response . PHP_EOL;
          //  flog(response);
        }
        // Rest of your code for processing the IRC response goes here
        if(strpos($message,'!x')!== false ){
            fwrite($socket, "PRIVMSG #$channel :I AM ALIVE\r\n");   
        }
        if(strpos($message,'time')!== false ){
            fwrite($socket, "PRIVMSG #$channel :". time() ."\r\n");   
        }
        


    }

    // Connection closed, attempt to reconnect
    echo "Disconnected from Twitch, reconnecting..." . PHP_EOL;
    flog('disconnected from Twitch');
    fclose($socket);
    $socket = connectToTwitch($server, $port, $timeout, $username, $pw);
    sleep(5);
}
?>




