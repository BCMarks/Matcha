<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM reports WHERE acc_id=:uide AND piemp_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$rep = $sth->fetchAll();

if(count($rep) == 0)
    echo "report user";
else
    echo "reported";
?>