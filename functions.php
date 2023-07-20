<?php

//short for file log  LUL 
function flog($what){
  file_put_contents('irc.log',$what.PHP_EOL, FILE_APPEND);
}

function botPID(){
  $cmd = 'pidof php irc.php';
  exec($cmd, $output);
  return $output;
}




?>