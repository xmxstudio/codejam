
SQL Query
<form action='query' method="POST">
<textarea rows=20  id="taQuery" name="sql">
<?php
$sql = $_POST['sql'];
$u = strtoupper($sql);
if(!strpos($u,'DROP') && !strpos($u,'ALTER') && strlen($u)) {
  $rows = $db->execute($sql);
  echo  $sql."\r\n\r\n\r\n";
  print_r($rows);
}else{
  echo "Select * from history order by timestamp desc limit 50";
}
?>
</textarea>
<button>Execute</button>
</form>