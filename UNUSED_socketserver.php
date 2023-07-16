<?php 
$address = '0.0.0.0';
$port = 12345;


$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, $address, $port);
socket_listen($server);

$clients = [];
function sendToAllClients($message, $clients){
    foreach ($clients as $client) {
        sendToClient($client['socket'], $message);
    }
}


function sendToClient($client, $message){
    $response = chr(129) . chr(strlen($message)) . $message;
    socket_write($client, $response, strlen($response));
}

function decodeWebSocketFrame($data){
    $payloadLength = ord($data[1]) & 127;
        if   ( $payloadLength === 126 ) { $mask = substr($data,  4, 4);  $payload = substr($data,  8);
    } elseif ( $payloadLength === 127 ) { $mask = substr($data, 10, 4);  $payload = substr($data, 14);
    } else {                              $mask = substr($data,  2, 4);  $payload = substr($data,  6);
    }
    $decodedPayload = '';
    for ($i = 0; $i < strlen($payload); $i++) {         $decodedPayload .= $payload[$i] ^ $mask[$i % 4]; }
    return $decodedPayload;
}

while (true) {
    $sockets = array_column($clients, 'socket');
    $sockets[] = $server;
    $write = $except = null;
    if (socket_select($sockets, $write, $except, null) === false) {break;}
    if (in_array($server, $sockets)) {
        $client = socket_accept($server);
        $request = socket_read($client, 5000);
        preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
        $key = '';
        if (isset($matches[1])) {
            $key = base64_encode(pack('H*', sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        }
        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Version: 13\r\n";
        $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";

        socket_write($client, $headers, strlen($headers));
        $clientId = uniqid();
        $clients[$clientId] = ['socket' => $client, 'handshake' => false];
        $index = array_search($server, $sockets);
        unset($sockets[$index]);
        sendToClient($client, "EHLO NURD! IDENTIFY YOSELF!\n");
    }
    foreach ($sockets as $clientSocket) {
        foreach ($clients as $clientId => $client) {
            if ($clientSocket == $client['socket']) {
                $data = socket_read($clientSocket, 1024);
                if ($data === false) {
                    echo "Error reading from client {$clientId}: " . socket_strerror(socket_last_error($clientSocket)) . "\n";
                    socket_close($clientSocket);
                    unset($clients[$clientId]);
                    echo "Client disconnected: {$clientId}\n";
                    break;
                }
                if (!$client['handshake']) {
                    $clients[$clientId]['handshake'] = true;
                } else {
                    $message = decodeWebSocketFrame($data);
                    echo "Received message from client {$clientId}: {$message}\n";
                    sendToAllClients($message, $clients);
                }
            }
        }
    }
}
?>