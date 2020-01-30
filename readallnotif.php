<?php

session_start();
include_once "config/database.php";
include_once "config/setup.php";

    $sql = 'SELECT * FROM notif WHERE n_uid=:nuid AND n_read = 0';
    $sth = $con->prepare($sql);
    $sth->bindParam(':nuid', $_SESSION['u_id']);
    $sth->execute();
    $notif = $sth->fetchAll();

    foreach($notif as $i => $v)
    {
        $sql = 'UPDATE notif SET n_read = 1 WHERE n_id=:nid';
        $sth = $con->prepare($sql);
        $sth->bindParam(':nid', $v['n_id']);
        $sth->execute();
    }
header('Location: notifs.php');
?>