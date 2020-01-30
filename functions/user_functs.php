<?php

function add_user($username, $fname, $lname, $email, $pass)
{
    include "config/database.php";

    $pass = hash('whirlpool', $pass."lol");
    $username = htmlspecialchars($username);
    $fname = htmlspecialchars($fname);
    $lname = htmlspecialchars($lname);
    $email = htmlspecialchars($email);

    try {
    $prep = $con->prepare(" INSERT INTO users (username, first_name, last_name, email, password)
                            VALUES (:name, :fname, :lname, :email, :pass);");

    $prep->bindParam(':name', $username);
    $prep->bindParam(':fname', $fname);
    $prep->bindParam(':lname', $lname);
    $prep->bindParam(':email', $email);
    $prep->bindParam(':pass', $pass);
    $prep->execute();

    $prep = $con->prepare(" INSERT INTO images (img1, img2, img3, img4, img5)
                            VALUES ('tmp/no_photo.png', 'tmp/no_photo.png', 'tmp/no_photo.png', 'tmp/no_photo.png', 'tmp/no_photo.png');");
    $prep->execute();

    $prep = $con->prepare(" INSERT INTO tags ()
                            VALUE ()");
    $prep->execute();
    }
    catch(PDOexception $err) {
        echo $err->getMessage();
    }
}

function in_members($email)
{
    include "config/database.php";

    $email = htmlspecialchars($email);

    $prep = $con->prepare(" SELECT email, username
                            FROM users
                            WHERE (email=:email) OR (username=:email);");
    $prep->bindParam(':email', $email);
    $prep->execute();
    $rows = $prep->rowCount();
    if ($rows == 1)
        return (TRUE);
    else
        return (FALSE);
}

function send_email($name, $email, $hash)
{
    $subject = "Account verification for Matcha";

    $message = "
    Hi, ".$name.".
     
    Thank you for creating an account with Matcha.

    To finalize your account creation, click on the link below.
    
    http://localhost:8080/matcha/sign-in.php?maggot=".$hash."&&email=".$email;

    $result = mail($email, $subject, $message, "From: no-reply@matcha.web.com");
    return ($result);
}

function get_data($username, $col)
{
        include "config/database.php";

        $username = htmlspecialchars($username);

        $prep = $con->prepare(" SELECT *
                                FROM users
                                WHERE username = :name");
        $prep->bindParam(':name', $username);
        $prep->execute();
        $data = $prep->fetch(PDO::FETCH_ASSOC);
        if ($col == -1)
            return ($data);
        return ($data[$col]);
}

function set_active($email)
{
    include "config/database.php";

    try {
        $prep = $con->prepare(" UPDATE users
                                SET active=1
                                WHERE (email=:email);");
        $prep->bindParam(':email', $email);
        $prep->execute();
    }
    catch(PDOexception $err) {
        echo "error: ".$err->getMessage();
    }
}

function update_user($u_id, $col, $data)
{

    include "config/database.php";

    if ($col == "password")
        $data = hash('whirlpool', $data."lol");
    else
        $data = htmlspecialchars($data);
    

    $prep = $con->prepare(" UPDATE users SET $col=:data WHERE user_id='$u_id';");
    $prep->bindParam(':data', $data);
    $prep->execute();
    return ("User Info has been updated.");
}

?>