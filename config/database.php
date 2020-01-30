<?php

$dsn = "mysql:host=localhost";
$sql_user = "root";
$sql_pass = "colinear";

$con = new PDO($dsn, $sql_user, $sql_pass);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE DATABASE IF NOT EXISTS matcha;";    
$con->exec($sql);

$dsn = "mysql:host=localhost;dbname=matcha";
$con = new PDO($dsn, $sql_user, $sql_pass);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>