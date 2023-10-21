<?php
require_once "db.php";
global $connection;
$query = $connection->prepare("SELECT count(code),number  FROM code  ");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
echo "-------------------------My MCI Bruteforce--------------------------------------- </br>";

$total = $data["count(code)"];
$number = $data["number"];


echo "Number : $number </br>";
echo "Total Code : $total</br>";

$query = $connection->prepare("SELECT count(status)  FROM code  WHERE status='1'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$test = $data["count(status)"];
$remain = ($test - 9999);
echo "Test : $test</br>";
echo "Remain : $remain</br>";

echo "-------------------------Error Type--------------------------------------- </br>";

$query = $connection->prepare("SELECT count(msg)  FROM code  WHERE msg='OTP'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$otp = $data["count(msg)"];
echo "Incorrect OTP : $otp</br>";

$query = $connection->prepare("SELECT count(msg)  FROM code  WHERE msg='General'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$general = $data["count(msg)"];
echo "General error : $general</br>";


$query = $connection->prepare("SELECT count(msg)  FROM code  WHERE msg='IP'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$ip = $data["count(msg)"];
echo "Limit IP : $ip</br>";

$query = $connection->prepare("SELECT count(msg)  FROM code  WHERE msg='OK'");
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);
$ok = $data["count(msg)"];
echo "Success : $ok</br>";

?>
