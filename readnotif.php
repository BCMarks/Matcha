<?php

include_once "config/database.php";
include_once "config/setup.php";

if(isset($_GET['n']))
{
    $sql = 'SELECT * FROM notif WHERE n_id=:nid';
    $sth = $con->prepare($sql);
    $sth->bindParam(':nid', $_GET['n']);
    $sth->execute();
    $notif = $sth->fetchAll();
    if (count($notif) == 1)
    {
        $sql = 'UPDATE notif SET n_read = 1 WHERE n_id=:nid';
        $sth = $con->prepare($sql);
        $sth->bindParam(':nid', $_GET['n']);
        $sth->execute();
    }
}
header('Location: notifs.php');
?>