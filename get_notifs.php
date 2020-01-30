<?php
    include_once "config/database.php";
    include_once "config/setup.php";
    session_start();

    $notify = 0;

    if (isset($_SESSION['u_id']))
    {
        $sql = 'SELECT * FROM notif JOIN users ON notif.n_uid = users.user_id WHERE users.user_id=:uide AND n_read = 0 ORDER BY n_id DESC';
        $sth = $con->prepare($sql);
        $sth->bindParam(':uide', $_SESSION['u_id']);
        $sth->execute();
        $unotifs = $sth->fetchAll();
        $notify = count($unotifs);
    }

    echo "NOTIFICATIONS[".$notify."]";
?>