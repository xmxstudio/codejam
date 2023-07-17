<?php
$br = '<br/>';
$total = $db->getEntryCount();
echo "Chat Entries: ".$total[0]->total;
echo $br;
echo "Unique Channel Entries: ".$db->execute('SELECT count(DISTINCT(channel)) as total from history')[0]->total;
// $db->getHistory('xmetrix');
echo $br;
$msg = $db->execute('SELECT * from history order by timestamp desc limit 1')[0];
$rmsg = $db->execute('SELECT * from history order by RAND() desc limit 1')[0];
// var_dump($msg);
echo "Last Chat Entry: ".$msg->author.": ". $msg->message;
echo $br;
echo "Random Chat Entry: ".$rmsg->author.": ". $rmsg->message;

?>