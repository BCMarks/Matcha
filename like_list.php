<?php
include_once "config/database.php";
include_once "config/setup.php";
session_start();
if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
    header('Location: sign-in.php');

$sql = 'SELECT * FROM likes WHERE liker_id=:uide OR likee_id=:uide ORDER BY like_id DESC';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->execute();
$likes = $sth->fetchAll();

?>
<html>
    <head>
        <link rel="stylesheet" href="css/home_style.css"/>
        <link rel="stylesheet" href="css/ll_style.css"/>
    </head>
    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>
            <div class="list">
                <div class="nice">PEOPLE YOU LIKE</div>
                <div class="naughty">PEOPLE WHO LIKE YOU</div>
                <div class="nice">
                <?php
                    foreach ($likes as $i => $v)
                    {
                        if ($_SESSION['u_id'] == $v['liker_id'])
                            $id = $v['likee_id'];
                        else
                            continue;
                        $sql = 'SELECT * FROM users WHERE `user_id`=:usid';
                        $sth = $con->prepare($sql);
                        $sth->bindParam(':usid', $id);
                        $sth->execute();
                        $user = $sth->fetchAll();
                ?>
                        <a href="profile.php?id=<?php echo $id?>">
                            <h4><?php echo $user[0]['first_name']." ".$user[0]['last_name']." AKA ".$user[0]['username']?></h4>
                        </a>
                        <br />
                <?php
                    }
                ?>
                </div>
                <div class="naughty">
                <?php
                    foreach ($likes as $i => $v)
                    {
                        if ($_SESSION['u_id'] == $v['likee_id'])
                            $id = $v['liker_id'];
                        else
                            continue;
                        $sql = 'SELECT * FROM users WHERE `user_id`=:usid';
                        $sth = $con->prepare($sql);
                        $sth->bindParam(':usid', $id);
                        $sth->execute();
                        $user = $sth->fetchAll();
                ?>
                        <a href="profile.php?id=<?php echo $id?>">
                            <h4><?php echo $user[0]['first_name']." ".$user[0]['last_name']." AKA ".$user[0]['username']?></h4>
                        </a>
                        <br />
                <?php
                    }
                ?>
                </div>
            </div>
        <div>
    </body>
</html>