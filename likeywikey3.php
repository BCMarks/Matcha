<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM likes WHERE likee_id=:uide AND liker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_POST['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$like = $sth->fetchAll();

$sql = 'SELECT * FROM likes WHERE likee_id=:uide AND liker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':us', $_POST['id']);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->execute();
$likeback = $sth->fetchAll();

$sql = 'SELECT * FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':us', $_POST['id']);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->execute();
$blok = $sth->fetchAll();

if(count($like) == 0)
{
    $sql = 'INSERT INTO likes(likee_id, liker_id) VALUES (:uide, :us)';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_POST['id']);
    $sth->bindParam(':us', $_SESSION['u_id']);
    $sth->execute();

    if(count($likeback) == 1)
    {
        if(count($blok) == 0)
        {
            #notify liked user of match
            $msg = $_SESSION['username']." has liked you! Congrats on the match :) Start chatting!";
            $prep = $con->prepare('INSERT INTO notif(n_uid, n_cont, n_date) VALUES (:uide, :cont, CURRENT_TIMESTAMP)');
            $prep->bindParam(':uide', $_POST['id']);
            $prep->bindParam(':cont', $msg);
            $prep->execute();
        }

        $sql = 'SELECT * FROM chat WHERE (u1_id=:uide AND u2_id=:uidd) OR (u1_id=:uidf AND u2_id=:uidg);';
        $sth = $con->prepare($sql);
        $sth->bindParam(':uide', $_SESSION['u_id']);
        $sth->bindParam(':uidd', $_POST['id']);
        $sth->bindParam(':uidg', $_SESSION['u_id']);
        $sth->bindParam(':uidf', $_POST['id']);
        $sth->execute();
        $chats = $sth->fetchAll();

        if(count($chats) == 0)
        {
            $tmp = "tmp.png";
            $prep = $con->prepare('INSERT INTO chat(u1_id, u2_id, paint) VALUES (:uide, :uidd, :tmp)');
            $prep->bindParam(':uide', $_SESSION['u_id']);
            $prep->bindParam(':uidd', $_POST['id']);
            $prep->bindParam(':tmp', $tmp);
            $prep->execute();
        }
        else
        {
            $prep = $con->prepare("UPDATE chat SET active = 1 WHERE (u1_id=:uide AND u2_id=:uidd) OR (u1_id=:uidf AND u2_id=:uidg);");
            $prep->bindParam(':uidg', $_SESSION['u_id']);
            $prep->bindParam(':uidf', $_POST['id']);
            $prep->bindParam(':uide', $_SESSION['u_id']);
            $prep->bindParam(':uidd', $_POST['id']);
            $prep->execute();
        }

        $sql = 'SELECT * FROM chat WHERE (u1_id=:uide AND u2_id=:uidd) OR (u1_id=:uidf AND u2_id=:uidg);';
        $sth = $con->prepare($sql);
        $sth->bindParam(':uide', $_SESSION['u_id']);
        $sth->bindParam(':uidd', $_POST['id']);
        $sth->bindParam(':uidg', $_SESSION['u_id']);
        $sth->bindParam(':uidf', $_POST['id']);
        $sth->execute();
        $chats2 = $sth->fetchAll();

        $p = $chats2[0]['chat_id'].".png";

        $prep = $con->prepare("UPDATE chat SET paint = :p WHERE (u1_id=:uide AND u2_id=:uidd) OR (u1_id=:uidf AND u2_id=:uidg);");
        $prep->bindParam(':p', $p);
        $prep->bindParam(':uide', $_SESSION['u_id']);
        $prep->bindParam(':uidd', $_POST['id']);
        $prep->bindParam(':uidg', $_SESSION['u_id']);
        $prep->bindParam(':uidf', $_POST['id']);
        $prep->execute();

        fopen("paint/".$p, "w");
    }
    else
    {
        if(count($blok) == 0)
        {
            #notify liked user of like
            $msg = $_SESSION['username']." has liked you!";
            $prep = $con->prepare('INSERT INTO notif(n_uid, n_cont, n_date) VALUES (:uide, :cont, CURRENT_TIMESTAMP)');
            $prep->bindParam(':uide', $_POST['id']);
            $prep->bindParam(':cont', $msg);
            $prep->execute();
        }
    }
}
else
{
    if(count($likeback) == 1)
    {
        $prep = $con->prepare("UPDATE chat SET active = 0 WHERE (u1_id=:uide AND u2_id=:uidd) OR (u1_id=:uidf AND u2_id=:uidg);");
        $prep->bindParam(':uide', $_SESSION['u_id']);
        $prep->bindParam(':uidd', $_POST['id']);
        $prep->bindParam(':uidg', $_SESSION['u_id']);
        $prep->bindParam(':uidf', $_POST['id']);
        $prep->execute();

        if(count($blok) == 0)
        {
            #notify unliked user of unmatch
            $msg = $_SESSION['username']." has unliked you! Match broken :'(";
            $prep = $con->prepare('INSERT INTO notif(n_uid, n_cont, n_date) VALUES (:uide, :cont, CURRENT_TIMESTAMP)');
            $prep->bindParam(':uide', $_POST['id']);
            $prep->bindParam(':cont', $msg);
            $prep->execute();
        }
    }
    $sql = 'DELETE FROM likes WHERE likee_id=:uide AND liker_id=:us';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_POST['id']);
    $sth->bindParam(':us', $_SESSION['u_id']);
    $sth->execute();
}
?>