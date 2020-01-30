<?php
    include_once "config/database.php";
    include_once "config/setup.php";
    session_start();

    if (isset($_SESSION['u_id']) && isset($_GET['k']))
    {
        $sql = 'SELECT * FROM msgs WHERE chat_id=:cid ORDER BY msg_id DESC';
        $sth = $con->prepare($sql);
        $sth->bindParam(':cid', $_GET['k']);
        $sth->execute();
        $msgs = $sth->fetchAll();
    }

    $html = "";
    foreach ($msgs as $i => $v)
    {
        if ($_SESSION['u_id'] == $v['to_id'])
        {
            $id = $v['from_id'];
            $class = "left";
        }
        else
        {
            $id = $v['to_id'];
            $class = "right";
        }
        #get info ofuser chatted with
        $sql = 'SELECT * FROM users WHERE `user_id`=:usid';
        $sth = $con->prepare($sql);
        $sth->bindParam(':usid', $id);
        $sth->execute();
        $msger = $sth->fetchAll();
    
        $html .= "<div class=".$class.">";
        if($class == "left")
            $html .= "<p>".$msger[0]['username']."--".$v['msg_time']."</p>";
        else
            $html .= "<p>".$_SESSION['username']."--".$v['msg_time']."</p>";
        $html .= "<p>".$v['content']."</p><br /></div>";
    }
    echo $html;
?>