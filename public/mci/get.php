<?php
require_once "db.php";
global $connection;
$query = $connection->prepare("SELECT count(msg)  FROM code  WHERE msg='OK' or   msg='General' or  msg='Client'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$ok = $data["count(msg)"];
var_dump($data);
if ($ok >= 1) {
      echo "Found";
} else {
      $query = $connection->prepare("SELECT code  FROM code WHERE status=0 ORDER BY rand()  LIMIT 1 ");
    $query->execute();
    $data = $query->fetch(PDO::FETCH_ASSOC);
    echo $data['code'];

}
