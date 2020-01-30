<?php
    include_once "config/database.php";
    include_once "config/setup.php";
    session_start();
    $prep = $con->prepare("UPDATE users SET avail = 0, last_log = CURRENT_TIMESTAMP WHERE user_id=:uide;");
    $prep->bindParam(':uide', $_SESSION['u_id']);
    $prep->execute();

    $_SESSION['username'] = "";
    $_SESSION['u_id'] = "";
    header('Location: index.php');
?>