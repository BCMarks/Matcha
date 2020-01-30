<?php
    include_once "config/database.php";
    include_once "config/setup.php";
    session_start();

    $sql = 'SELECT * FROM notif JOIN users ON notif.n_uid = users.user_id WHERE users.user_id=:uide AND n_read = 1 ORDER BY n_id DESC';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_SESSION['u_id']);
    $sth->execute();
    $rnotifs = $sth->fetchAll();
    
    $sql = 'SELECT * FROM notif JOIN users ON notif.n_uid = users.user_id WHERE users.user_id=:uide AND n_read = 0 ORDER BY n_id DESC';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_SESSION['u_id']);
    $sth->execute();
    $unotifs = $sth->fetchAll();
    
    $html = "";
    foreach ($unotifs as $i => $v)
    {
        $html .= "<div class=\"unread\" onclick=\"window.location.href = 'readnotif.php?n=".$v['n_id']."'\">
        <h4>".$v['n_cont']." | ".$v['n_date']."</h4><br /></div>";
    }
    foreach ($rnotifs as $i => $v)
    {
        $html .= "<div class=\"read\"><h4>".$v['n_cont']." | ".$v['n_date']."</h4><br /></div>";
    }

    echo $html;
?>