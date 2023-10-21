<?php
require_once "db.php";
global $connection;
if (isset($_GET['number']) and !empty($_GET['number'])) {
    $number = $_GET['number'];
    $query = $connection->prepare("UPDATE `code` SET `number`=? , `status`='',`msg`='',`data`='' ");
    $query->bindValue(1, $number);
    $query->execute();
}
