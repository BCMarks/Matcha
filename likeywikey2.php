<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM likes WHERE likee_id=:uide AND liker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$like = $sth->fetchAll();

if(count($like) == 0)
    echo "like user";
else
    echo "unlike user";
?>