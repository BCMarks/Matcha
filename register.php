<?php
    session_start();

    include_once "config/database.php";
    include_once "config/setup.php";
    include_once "functions/user_functs.php";
    
    $error = "";
    
    if (isset($_POST['username']) && isset($_POST['first_name']) && isset($_POST['last_name']) && 
        isset($_POST['email']) && isset($_POST['password']) && isset($_POST['conpassword'])) {
                
        $pass = $_POST["password"];
        $conpass = $_POST["conpassword"];
        $name = $_POST["username"];
        $fname = $_POST["first_name"];
        $lname = $_POST["last_name"];
        $email = $_POST["email"];
    
        $special_pass = preg_match('@[^\w]@', $pass);
    
        if (in_members($email))
            $error = "Account with email or username already exist.";
        else if ($special_pass == 0)
            $error = "Password must contain a special character.";
        else if ($pass != $conpass)
            $error = "Password and Confirm Password does not match.";
        //If there are no errors, add user's data to database and send activation link to user's email.
        else
        {
            add_user($name, $fname, $lname, $email, $pass);
            $hash = hash('whirlpool', $email);
            $_SESSION["hash"] = $hash;
            send_email($name, $email, $hash);
            $error = "A link has been sent to your email.";
        }
    }
?>

<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/reg_style.css"/>

    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>

            <form class="reg_form" method="post" action="register.php">
                <span class="form_title"><h1>Register</h1></span>
                <span class="error"><h2><?php echo $error; ?></h2></span>
                <label>Username: </label>
                <input type="text" name="username" autocomplete required>
                <label>First Name: </label>
                <input type="text" name="first_name" autocomplete required>
                <label>Last Name: </label>
                <input type="text" name="last_name" autocomplete required>
                <label>Email: </label>
                <input type="email" name="email" autocomplete required/>
                <label>Password: </label>
                <input type="password" name="password" minlength="8" autocomplete required/>
                <label>Confirm Password: </label>
                <input type="password" name="conpassword" minlength="8" autocomplete required/>
                <button type="submit" name="submit" ><h2>CONFIRM</h2></button>
            </form>

        </div>
    </body>
</html>