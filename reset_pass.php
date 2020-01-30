<?php
    include_once "config/database.php";
    include_once "config/setup.php";

    session_start();

    $code_confirmed = FALSE;
    $page_url = "reset_pass.php";
    $error = "";

    if (isset($_GET["var"]) && isset($_SESSION["code"]) && isset($_SESSION["token"]) && isset($_SESSION["forpassname"]) && ($_GET["var"] == $_SESSION["token"]))
    {
        $page_url = "reset_pass.php?var=".$_SESSION["token"];
        $error = "An email has been sent to you with reset code.";

        if (isset($_POST["code"]))
        {
            if ($_SESSION["code"] == $_POST["code"])
                $code_confirmed = TRUE;
        }

        if (isset($_POST["password"]))
        {
            $pass = $_POST["password"];
            $special_pass = preg_match('@[^\w]@', $pass);

            $pass = hash('whirlpool', $pass."lol");
            $name = $_SESSION["forpassname"];

            if ($special_pass == 0)
                $error = "Password must contain a special character.";
            else 
            {
                $prep = $con->prepare(" UPDATE users
                                        SET password='$pass'
                                        WHERE (username='$name');");
                $prep->execute();
                unset($_SESSION["code"]);
                unset($_SESSION["token"]);
                unset($_SESSION["forpassname"]);
                header("Location: sign-in.php");
            }
        }
    }
    else
        header("Location: index.php");
?>


<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/respass.css"/>

    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>

           <?php if ($code_confirmed === FALSE) { ?>
                <form class="repass_form" style=" height: 240px; " action=<?php echo $page_url; ?> method="post">
                    <span class="error"><h2><?php echo $error; ?></h2></span>
                    <label>Reset Code: </label>
                    <input type="text" name="code" maxlength="5" required/>
                    <button type="submit" name="submit" ><h3>CONFIRM</h3></button>
                </form>
           <?php } else { ?>
                <form class="repass_form" action=<?php echo $page_url; ?> method="post">
                    <label>New Password: </label>
                    <input type="password" name="password" minlength="8" autocomplete required/>
                    <button type="submit" name="submit" ><h3>CONFIRM</h3></button>
                </form>
           <?php }?>
        </div>
    </body>

</html>