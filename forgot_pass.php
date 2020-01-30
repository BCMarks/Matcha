<?php
    include_once "config/database.php";
    include_once "config/setup.php";

    session_start();

    function send_code_email($code, $name, $email)
    {
        $subject = "Reset Password for Matcha";

        $message = "
        Hi, ".$name.".
         
        It seems you have forgotten your password.

        Here is the code to reset your password.
    
        ".$code;
    
        echo $message;
        $result = mail($email, $subject, $message, "From: no-reply@matcha.web.com");
    }

    //if username and email in user database,
    //send email to user's email address, that contains a unique code to reset password.
    if (isset($_POST["username"]) && isset($_POST["email"]))
    {
        $username = htmlspecialchars($_POST["username"]);
        $email = htmlspecialchars($_POST["email"]);

        $prep = $con->prepare("SELECT username, email
                                FROM users
                                WHERE (username=:username) && (email=:email);");
        $prep->bindParam(':username', $username);
        $prep->bindParam(':email', $email);
        $prep->execute();

        $rows = $prep->rowCount();
        if ($rows)
        {
            $_SESSION["code"] = rand(10000, 99999);
            $_SESSION["token"] = hash('whirlpool', rand(123, 321));
            $_SESSION["forpassname"] = $username;
            send_code_email($_SESSION["code"], $username, $email);
            header("Location: reset_pass.php?var=".$_SESSION["token"]);
        }
    }


?>

<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/forpass_style.css">

    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>

            <form class="pass_form" action="forgot_pass.php" method="post">
                <label>Username: </label>
                <input type="text" name="username" autocomplete required/>
                <label>Email: </label>
                <input type="email" name="email" autocomplete required/>
                <button type="submit" name="submit" ><h3>CONFIRM</h3></button>
            </form>
        </div>
    </body>
</html>