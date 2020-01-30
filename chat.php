<?php
include_once "config/database.php";
include_once "config/setup.php";
session_start();
if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
    header('Location: sign-in.php');
if(isset($_GET['k']))
{
    $sql = 'SELECT * FROM chat WHERE (u1_id=:uide OR u2_id=:uidf) AND chat_id=:cid';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_SESSION['u_id']);
    $sth->bindParam(':uidf', $_SESSION['u_id']);
    $sth->bindParam(':cid', $_GET['k']);
    $sth->execute();
    $chat = $sth->fetchAll();
    if(count($chat) == 0)
        header('Location: messages.php');
}
else
    header('Location: messages.php');

if(isset($_POST['submit']))
{
    if($_POST['submit'] == "Post" && isset($_POST['cmnt']))
    {
        $clean = htmlspecialchars($_POST['cmnt']);
        try
        {
            if ($_SESSION['u_id'] == $chat[0]['u1_id'])
                $id = $chat[0]['u2_id'];
            else
                $id = $chat[0]['u1_id'];
            $sql = 'INSERT INTO msgs (chat_id, to_id, from_id, content, msg_time) VALUES (:cid, :tid, :fid, :cmnt, CURRENT_TIMESTAMP);';
            $sth = $con->prepare($sql);
            $sth->bindParam(':cid', $_GET['k']);
            $sth->bindParam(':tid', $id);
            $sth->bindParam(':fid', $_SESSION['u_id']);
            $sth->bindParam(':cmnt', $clean);
            $sth->execute();

            $prep = $con->prepare("UPDATE chat SET last_msg = CURRENT_TIMESTAMP WHERE chat_id=:cid;");
            $prep->bindParam(':cid', $_GET['k']);
            $prep->execute();

            $sql = 'SELECT * FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';
            $sth = $con->prepare($sql);
            $sth->bindParam(':us', $id);
            $sth->bindParam(':uide', $_SESSION['u_id']);
            $sth->execute();
            $reblok = $sth->fetchAll();

            if(count($reblok) == 0)
            {  
                #notifications of new message
                $msg = $_SESSION['username']." has sent a new message.";
                $prep = $con->prepare('INSERT INTO notif(n_uid, n_cont, n_date) VALUES (:uide, :cont, CURRENT_TIMESTAMP)');
                $prep->bindParam(':uide', $id);
                $prep->bindParam(':cont', $msg);
                $prep->execute();
            }

            header('Location: chat.php?k='.$_GET['k']);
        }
        catch (PDOException $e)
        {
            echo "<script type='text/javascript'>alert('Connection failed: ".$e->getMessage()."');</script>";
        }
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="css/home_style.css"/>
        <link rel="stylesheet" href="css/msg_style.css"/>
    </head>
    
    <body>
        <div class="msgmain">
            <?php include_once "head_foot.php"; ?>
            <div class="chats" id ="chats">
                
            </div>
            <!--if chat active-->
            
            <div class="new">
            <?php
            if($chat[0]['active'] == 1)
            {
                ?>
                <form id="formo" autocomplete="off" method="POST">
                    <textarea name="cmnt" style="resize:none" required></textarea>
                    <input type="submit" name="submit" value="Post" />
                </form>
                <div id="paint" onclick="window.location.href='paint.php?k=<?php echo $_GET['k']; ?>'">DRAW</div>
                <?php
            }
            else
            {
                ?>
                CHAT IS CURRENTLY DISABLED DUE TO UNMATCHING. SORRY~
                <?php
            }
            ?>
                
            </div>
        <div>
    </body>
    <script>
        function msgs() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("chats").innerHTML = this.responseText;
            }
            };
            xmlhttp.open("GET", "get_messages.php?k=<?php echo $_GET['k'];?>", true);
            xmlhttp.send();
        }
        msgs();
        setInterval(msgs, 1000);
    </script>
</html>