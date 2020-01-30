<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

$sql = 'SELECT * FROM likes WHERE likee_id=:uide AND liker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$like = $sth->fetchAll();

$sql = 'SELECT * FROM likes WHERE liker_id=:uide AND likee_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->bindParam(':us', $_SESSION['u_id']);
$sth->execute();
$likeb = $sth->fetchAll();

if($_SESSION['u_id'] != $_GET['id'])
{
    if(count($like) != 0 && count($likeb) != 0)
        echo "<div class=\"match\">MATCHED!</div>";
    else
        echo "<div class=\"match\" style=\"background-color:inherit\"></div>";
}
else
    echo "<div class=\"match\" onclick=\"window.location.href='like_list.php'\"><h3>Like List</h3></div>";
?>