<?php
session_start();
include_once 'functions.php';
include_once 'db.php';

$uri = $_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@200;400&display=swap" rel="stylesheet">
</head>

<body>
<header>
<?php include 'nav.php';?>
</header>  
<div id="main">
<?php

switch ($uri){
  case '/': 
    include_once 'pages/home.php';
    break;
  case '/emotes':
    include_once 'pages/emotes.php';
    break;
  case '/query':
    include_once 'pages/query.php';
    break;
  case '/config':
    include_once 'pages/config.php';
    break;
  case '/x':
    session_destroy();
    header('Location: /');
    break;
  case '/add':
    $channel = trim($_POST['channel']);
    flog('ADDING CHANNEL '.$channel);
    $db->addChannel($channel);
    header('Location: /config');
    break;
  case '/remove':
    $channel = $_POST['channel'];
    flog('REMOVING CHANNEL '.$channel);
    $db->removeChannel($channel);
    header('Location: /config');
    break;
  case '/connect':
    if (!empty($pid)) {
      header('Location: /');
      exit;
    }
    flog('LAUNCHING IRC CONNECTION');
    exec("nohup php irc.php > /dev/null 2>&1 &", $output, $returnCode);
    var_dump($output);
    echo "Return code: $returnCode";
    //header('Location: '.$_SESSION['lastURI']);
    break;
  case '/disconnect':
    flog('TERMINATING IRC CONNECTION');
    unset($_SESSION['channels']);
    if (empty($pid)) {
      header('Location: /');
      exit;
    }
    exec("kill ".$pid);
    header('Location: '.$_SESSION['lastURI']);
    break;
  default:
    
    $fof= true;
    http_response_code(404);
    echo "Nerdgasm not Founded!";

}
$_SESSION['lastURI'] = $_SERVER['REQUEST_URI'];
?>
</div>

</body>
</html>
