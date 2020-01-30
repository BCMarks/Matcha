<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$blok = $sth->fetchAll();

if(count($blok) == 0)
    echo "block user";
else
    echo "unblock user";
?>