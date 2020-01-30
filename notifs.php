<?php
    include_once "config/database.php";
    include_once "config/setup.php";
    session_start();
    if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
        header('Location: sign-in.php');

?>
<html>
    <head>
        <link rel="stylesheet" href="css/home_style.css"/>
        <link rel="stylesheet" href="css/notif_style.css"/>
    </head>
    <body>
        <div class="nmain">
            <?php include_once "head_foot.php"; ?>
            <div class="notifs" id="notifs"> </div>
            <div class="clear" id="clear" onclick="window.location.href = 'readallnotif.php'"><span>MARK ALL AS READ</span></div>
        <div>
    </body>
    <script>
        function disp() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("notifs").innerHTML = this.responseText;
            }
            };
            xmlhttp.open("GET", "get_notifs_disp.php", true);
            xmlhttp.send();
        }
        disp();
        setInterval(disp, 1000);
    </script>
</html>