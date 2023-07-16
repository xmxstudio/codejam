<?php
include_once 'functions.php';
$pid = botPID();
$rows=  $db->getConfig();
// var_dump($rows);
if(count($rows)){
?>
Channels configured for autojoin (click to remove)<br/><br/>
<form action="/remove" method="POST"> 
<?php 
    foreach ($rows as $row){
?>
    <button name="channel" value="<?= $row->channel ?>"><?= $row->channel ?></button>
      <!-- <a href="/remove?channel=<?= $row->channel ?>"><?= $row->channel?></a><br/> -->
<?php    
  }

}
?>
</form>
<form action="/add" method="POST" >
  <input type="text" name="channel" placeholder="channel name"><br/>
  <button>Add Channel</button>
  
  <?php if($pid){
  ?>
  <button name="reconnect">Reconnect and join all chats</button>
  <?php
  }
  ?>  
  
</form>