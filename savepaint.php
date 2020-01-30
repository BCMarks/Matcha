<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

if (isset($_POST['k']) && isset($_POST['img']))
{
    $sql = 'SELECT * FROM chat WHERE (u1_id=:uide OR u2_id=:uidf) AND chat_id=:cid';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_SESSION['u_id']);
    $sth->bindParam(':uidf', $_SESSION['u_id']);
    $sth->bindParam(':cid', $_POST['k']);
    $sth->execute();
    $chat = $sth->fetchAll();

    if(count($chat) != 0 )
    {
        $p = $_POST['k'].".png";
        $sql = 'UPDATE chat SET paint = :p WHERE chat_id=:cid;';
        $sth = $con->prepare($sql);  
        $sth->bindParam(':p', $p);
        $sth->bindParam(':cid', $_POST['k']);
        $sth->execute();

        //save to k.png in paint folder
        $img = $_POST['img'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $data = base64_decode($img);
        $src = "paint/".$p;
        file_put_contents($src, $data);
    }
}
?>