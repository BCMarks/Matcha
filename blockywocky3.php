<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_POST['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$blok = $sth->fetchAll();

$sql = 'SELECT * FROM likes WHERE likee_id=:uide AND liker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_POST['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$like = $sth->fetchAll();

if(count($blok) == 0)
    $sql = 'INSERT INTO blocked(blockee_id, blocker_id) VALUES (:uide, :us)';
else
    $sql = 'DELETE FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';

$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_POST['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
?>