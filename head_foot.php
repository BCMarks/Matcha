<?php
    include_once "config/database.php";
    include_once "config/setup.php";
?>
<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    
    <header class="head">
            <img class="logo" src="logo.png" onclick="window.location.href='index.php'" />
            <div class="butts">
                <?php if (isset($_SESSION["u_id"]) && $_SESSION["u_id"] != "")  { ?>
                    <script>
                        function notify() {
                            var xmlhttp = new XMLHttpRequest();
                            xmlhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                document.getElementById("notif").innerHTML = this.responseText;
                            }
                            };
                            xmlhttp.open("GET", "get_notifs.php", true);
                            xmlhttp.send();
                        }
                        notify();
                        setInterval(notify, 1000);
                    </script>
                    <div class="notif" onclick="window.location.href='notifs.php'" ><h3 id="notif"></h3></div>
                    <div class="msgs" onclick="window.location.href='messages.php'" ><h3>MESSAGES</h3></div>
                    <div class="prof" onclick="window.location.href='profile.php?id=<?php echo $_SESSION['u_id']; ?>'" ><h3>PROFILE</h3></div>
                    <div class="bro" onclick="window.location.href='browser.php'" ><h3>BROWSER</h3></div>
                    <div class="out" onclick="window.location.href='logout.php'" ><h3>LOGOUT</h3></div>
                <?php } else { ?>
                    <div class="reg" onclick="window.location.href='register.php'"><h3>REGISTER</h3></div>
                    <div class="sign" onclick="window.location.href='sign-in.php'"><h3>SIGN-IN</h3></div>
                <?php } ?>
            </div>
    </header>

    <footer class="foot">
        <hr>
        <pre>&copy;bmarks & iisaacs 2019-2020</pre>
    </footer>
</html>