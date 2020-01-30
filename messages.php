<?php
include_once "config/database.php";
include_once "config/setup.php";
session_start();
if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
    header('Location: sign-in.php');

$sql = 'SELECT * FROM chat WHERE u1_id=:uide OR u2_id=:uidf ORDER BY last_msg DESC';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->bindParam(':uidf', $_SESSION['u_id']);
$sth->execute();
$chats = $sth->fetchAll();
#array of all chats user is involved with

?>
<html>
    <head>
        <link rel="stylesheet" href="css/home_style.css"/>
        <link rel="stylesheet" href="css/msg_style.css"/>
    </head>
    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>
            <!-- links to different chats, setting k as chat_id -->
            <div class="chats">
                <?php
                    foreach ($chats as $i => $v)
                    {
                        if ($_SESSION['u_id'] == $v['u1_id'])
                            $id = $v['u2_id'];
                        else
                            $id = $v['u1_id'];
                        #get info of each user chatted with
                        $sql = 'SELECT * FROM users WHERE `user_id`=:usid';
                        $sth = $con->prepare($sql);
                        $sth->bindParam(':usid', $id);
                        $sth->execute();
                        $msger = $sth->fetchAll();
                ?>
                    <div class="cmsg">
                        <!--user image, first name, last name, username-->
                        <a href="chat.php?k=<?php echo $v['chat_id']?>">
                            <h4><?php echo $msger[0]['first_name']." ".$msger[0]['last_name']." AKA ".$msger[0]['username']?></h4>
                        </a>
                        <br />
                    </div>
                <?php
                    }
                ?>
            </div>
        <div>
    </body>
</html>