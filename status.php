<?php

include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM users JOIN images ON images.img_uid = users.user_id WHERE user_id=:uide AND active = 1';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->execute();
$info = $sth->fetchAll();

$a = $info[0]['avail'];
$b = $info[0]['last_log'];

if($a == 1)
    echo "<h2>Status:</h2> <p>ONLINE</p>";
else
    echo "<h2>Status:</h2><p>".$b."</p>";
?>