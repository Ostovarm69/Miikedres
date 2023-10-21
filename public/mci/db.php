<?php
$dsn = "mysql:host=localhost;dbname=mikedres_mbrute";
$username = "mikedres_mbrute";
$password = "r0hollaHhH@##";
try {
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $msg) {
    echo "Error " . $msg->getMessage();
}
