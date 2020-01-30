<?php
include_once "config/database.php";
include_once "config/setup.php";
session_start();
if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
    header('Location: sign-in.php');
if(isset($_GET['k']))
{
    $sql = 'SELECT * FROM chat WHERE (u1_id=:uide OR u2_id=:uidf) AND chat_id=:cid';
    $sth = $con->prepare($sql);
    $sth->bindParam(':uide', $_SESSION['u_id']);
    $sth->bindParam(':uidf', $_SESSION['u_id']);
    $sth->bindParam(':cid', $_GET['k']);
    $sth->execute();
    $chat = $sth->fetchAll();
    if(count($chat) == 0)
        header('Location: messages.php');
}
else
    header('Location: messages.php');

?>
<html>
    <head>
        <link rel="stylesheet" href="css/home_style.css"/>
        <link rel="stylesheet" href="css/msg_style.css"/>
    </head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <body>
        <div class="msgmain">
            <?php include_once "head_foot.php"; ?>
            <div class="chats">
                <canvas id="canvas" style="border: 2px solid black" onmousemove="keepLine()" onmouseup="drawLine()" onmousedown="startLine()" width="900" height="600" style="background-color:#ffffff;cursor:default;">

                </canvas>
            </div>
            <div class="new">
                <button id="black" onclick="getCol(this.id);">BLACK</button>
                <button id="red" onclick="getCol(this.id);">RED</button>
                <button id="blue" onclick="getCol(this.id);">BLUE</button>
                <button id="white" onclick="getCol(this.id);">WHITE</button>
                <button id="reset" onclick="reset();">RESET</button>
                <button id="back" onclick="window.location.href='chat.php?k=<?php echo $_GET['k']; ?>'">BACK TO CHAT</button>
            </div>
        <div>
    </body>
    <script>
        var x = 0;
        var y = 0;
        var clicked = false;

        var canvas = document.getElementById("canvas");
        var context = canvas.getContext("2d");

        context.strokeStyle = "black";
        context.lineCap = "round";

        canvas.addEventListener('mousemove', function(e) { getMousePos(canvas, e); }, false);

        function getCol(c) {
            context.strokeStyle = c;
        }

        function processImage(file) {
        var reader = new FileReader();
        reader.readAsDataUrl(file)
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
            context.drawImage(img, 100,100)
            }
            img.src = e.target.result;
        }
        }

        // functions for drawing (works perfectly well)
        function getMousePos(canvas, e) {
            var rect = canvas.getBoundingClientRect();
            x = e.clientX - rect.left;
            y = e.clientY - rect.top;
        }

        function startLine() {
            context.moveTo(x,y);
            context.beginPath();
            clicked = true;
        }

        function keepLine() {
            if(clicked) {
                context.lineTo(x,y);
                context.stroke();
                context.moveTo(x,y);
            }
        }

        function drawLine() {
            context.lineTo(x,y);
            context.stroke();
            clicked = false;
        }

        function reset() {
            context.clearRect(0,0,canvas.width,canvas.height);
        }

        function saveload() {
            var canvas = document.getElementById("canvas");
            var context = canvas.getContext("2d");
            
            //load image
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var image = new Image();
                image.onload = function() {
                context.drawImage(image, 0, 0);
                };
                image.src = this.responseText;
            }
            };
            xmlhttp.open("GET", "loadpaint.php?k=<?php echo $_GET['k'];?>", true);
            xmlhttp.send();

            //save image
            var str = canvas.toDataURL();
            $.ajax({
                url: "savepaint.php",
                data: {
                    k: <?php echo $_GET['k']?>,
                    img: str
                },
                type: "post"
            });
            // var xmlhttp = new XMLHttpRequest();
            // xmlhttp.open("POST", "savepaint.php", true);
            // xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            // xmlhttp.send("k=<?php //echo $_GET['k']?>&img="+str);

        }
        saveload();
        setInterval(saveload, 100);
    </script>
</html>