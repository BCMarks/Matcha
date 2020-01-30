<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM chat WHERE (u1_id=:uide OR u2_id=:uidf) AND chat_id=:cid';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->bindParam(':uidf', $_SESSION['u_id']);
$sth->bindParam(':cid', $_GET['k']);
$sth->execute();
$chat = $sth->fetchAll();

if($chat != NULL)
    echo "paint/".$chat[0]['paint'];

?>