<?php
    session_start();

    include_once "config/database.php";
    include_once "config/setup.php";
    include_once "functions/user_functs.php";

    $error = "";

    //If email and hash (maggot) are set in GET array...
    if (isset($_GET["email"]) && isset($_GET["maggot"]) && isset($_SESSION["hash"]))
    {
        $Shash = $_SESSION["hash"];
        $email = $_GET["email"];
        $hash = $_GET["maggot"];

        //and hash matches with the session's hash, activate account.
        if ($Shash == $hash)
        {
            set_active($email);
            unset($_SESSION["hash"]);
            $error = "Your account has been verified. Sign in.";
        }
    }

    //When the form is submitted..
    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["conpassword"]) && isset($_POST["uloc"]))
    {
        $name = $_POST['username'];
        $pass = $_POST["password"];
        $conpass = $_POST["conpassword"];
        
        $prep = $con->prepare(" SELECT username, active
                                FROM users
                                WHERE (username=:name) AND (active=1);");
        $prep->bindParam(':name', $name);
        $prep->execute();
        $active = $prep->rowCount(); //active is used to check if username (account) is active

        $hash_pass = hash('whirlpool', $pass."lol");
        $prep = $con->prepare(" SELECT username, password
                                FROM users
                                WHERE (username=:name) AND (password=:pass);");
        $prep->bindParam(':name', $name);
        $prep->bindParam(':pass', $hash_pass);
        $prep->execute();
        $is_pass = $prep->rowCount(); //is_pass checks if password matches username

        //If active and is_pass is TRUE create user session variables. Else display error.
        if ($active && $is_pass)
        {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['u_id'] = get_data($_SESSION['username'], "user_id");
            $_SESSION['uloc'] = $_POST['uloc'];
            
            $prep = $con->prepare("UPDATE users SET avail = 1 WHERE user_id=:uide;");
            $prep->bindParam(':uide', $_SESSION['u_id']);
            $prep->execute();

            $u_id = $_SESSION['u_id'];
            $prep = $con->prepare("SELECT last_log FROM users WHERE user_id='$u_id'");
            $prep->execute();
            $last_log = $prep->fetch();

            if ($last_log[0] != NULL)
                header("Location: index.php");
            else
                header("Location: profile_edit.php?id=".$_SESSION["u_id"]);

        }

        if ($pass != $conpass)
            $error = "Password does not match Confirm Password.";
        else
            $error = "Username does not exist or it is not active.";
    }

    
?>
<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/sign_style.css"/>
    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>

            <form class="sign_form" method="post" action="sign-in.php">
                <span class="form_title"><h1>Sign-In</h1></span>
                <span class="error"><h2><?php echo $error; ?></h2></span>
                <label>Username: </label>
                <input type="text" name="username" autocomplete required/>
                <label>Password: </label>
                <input type="password" name="password" minlength="8" autocomplete required/>
                <label>Confirm Password: </label>
                <input type="password" name="conpassword" minlength="8" autocomplete required/>
                <input id="uloc" name="uloc" type="hidden" value=""/>
                <button type="submit" name="submit" ><h2>CONFIRM</h2></button>
                <a href="forgot_pass.php">Forgot Password?</a>
            </form>

        </div>
    </body>
    <script language="JavaScript" src="http://www.geoplugin.net/javascript.gp" type="text/javascript"></script>
    <script type="text/javascript">
        document.getElementById('uloc').value = geoplugin_city()+", "+geoplugin_region()+", "+geoplugin_countryName();
    </script>
</html>