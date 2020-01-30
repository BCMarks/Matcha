<?php
include_once "config/database.php";
include_once "config/setup.php";

session_start();

if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
{
    header('Location: sign-in.php');
    exit;
}
if(!isset($_GET['id']))
    header('Location: index.php');

$sql = 'SELECT * FROM users JOIN images ON images.img_uid = users.user_id WHERE user_id=:uide AND active = 1';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->execute();
$info = $sth->fetchAll();
#user info and images

if(count($info) == 0)
{
    header('Location: index.php');
    exit;
}
    
$sql = 'SELECT * FROM blocked WHERE blockee_id=:uide AND blocker_id=:us';
$sth = $con->prepare($sql);
$sth->bindParam(':us', $_GET['id']);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->execute();
$reblok = $sth->fetchAll();

if($_SESSION['u_id'] != $_GET['id'])
{
    $sql = 'INSERT INTO visits(visiter_id, visitee_id, v_time) VALUES (:vid, :pid, CURRENT_TIMESTAMP)';
    $sth = $con->prepare($sql);
    $sth->bindParam(':vid', $_SESSION['u_id']);
    $sth->bindParam(':pid', $_GET['id']);
    $sth->execute();

    if(count($reblok) == 0)
    {        
        $msg = $_SESSION['username']." has viewed your profile!";
        $prep = $con->prepare('INSERT INTO notif(n_uid, n_cont, n_date) VALUES (:uide, :cont, CURRENT_TIMESTAMP)');
        $prep->bindParam(':uide', $_GET['id']);
        $prep->bindParam(':cont', $msg);
        $prep->execute();
    }
}

$sql = 'SELECT * FROM tags WHERE tag_uid=:uide';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->execute();
$tags = $sth->fetchAll();

$sql = 'SELECT * FROM visits JOIN users ON users.user_id = visits.visiter_id WHERE visitee_id=:uide ORDER BY visit_id DESC';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_GET['id']);
$sth->execute();
$vis = $sth->fetchAll();

$prep = $con->prepare("SELECT * FROM likes WHERE likee_id=:uide");
$prep->bindParam(':uide', $_GET['id']);
$prep->execute();
$like2 = $prep->fetchAll();
$prep = $con->prepare("SELECT * FROM blocked WHERE blockee_id=:uide");
$prep->bindParam(':uide', $_GET['id']);
$prep->execute();
$blok2 = $prep->fetchAll();

$fem = count($vis) + 5 * count($like2) - 8 * count($blok2);
if($fem < 0)
    $fem = 0;
if($fem > 900)
    $fem = 900;
$prep = $con->prepare("UPDATE users SET fame=:fem WHERE user_id=:uide;");
$prep->bindParam(':uide', $_GET['id']);
$prep->bindParam(':fem', $fem);
$prep->execute();

$sql = 'SELECT * FROM images WHERE img_uid=:uide';
$sth = $con->prepare($sql);
$sth->bindParam(':uide', $_SESSION['u_id']);
$sth->execute();
$yimg = $sth->fetchAll();
$yimg = $yimg[0];
$upcount = -2;

foreach($yimg as $i => $v)
{
    if($v != "tmp/no_photo.png")
        $upcount += 1;
}

?>
<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/profile_style.css"/>
    <body>
        <div class="promain">
            <?php include_once "head_foot.php"; ?>

            <div class="picdiv">
                <img id="img" onclick="cycle();" class="profimg" src=<?php echo $info[0]['img1']?>>
                <div id="tmp"></div>
                <?php
                //if user has uploaded images
                if($_SESSION['u_id'] != $_GET['id'])
                {
                    if($upcount != 0)
                    {
                        ?>
                        <div class="like" id="like" onclick="like();"></div>
                    <?php
                    }
                }
                if($_SESSION['u_id'] == $_GET['id'])
                {
                ?>
                <div class="edit" onclick="window.location.href='profile_edit.php?id=<?php echo $_GET['id']; ?>'"><h3>Edit your profile</h3></div>
                <?php
                }
                ?>
            </div>

            <div class="user_info">
                <div class="bio">
                    <h1>Biography</h1>
                    <pre><h3><?php echo $info[0]['bio']?></h3></pre>
                </div>
                <div class="info">
                    <p><h3>Username:  <?php echo $info[0]['username']?></h3></p>
                    <p><h3>Fame Rating:  <?php echo $info[0]['fame']?></h3></p>
                    <p><h3>First Name:  <?php echo $info[0]['first_name']?></h3></p>
                    <p><h3>Last Name:  <?php echo $info[0]['last_name']?></h3></p>
                    <p><h3>Age:  <?php echo $info[0]['age']?></h3></p>
                    <p><h3>Gender:  <?php echo $info[0]['gender']?></h3></p>
                    <p><h3>Location:  <?php echo $info[0]['location']?></h3></p>
                </div>
                <div class="status" id="status"></div>
            </div>
            
            <div class="tag_div">
                <div class="tags">
                    TAGS: 
                    <?php
                    if (count($tags) != 0)
                    {
                        $i = 1;
                        while ($i < 25)
                        {
                            if($tags[0]["tag$i"] != 0)
                            {
                                $sql = "SELECT * FROM taglist WHERE tag_id=$i";
                                $sth = $con->prepare($sql);
                                $sth->execute();
                                $tagname = $sth->fetchAll();
                                ?>
                                <p><?php echo $tagname[0]["tag_name"]?></p>
                                <?php
                            }
                            $i++;
                        }
                    }
                    ?>
                </div>
                <?php
                if($_SESSION['u_id'] == $_GET['id'])
                {
                ?>
                <div class="vis">
                    <p style="text-align:center">Visited:</p>
                    <?php
                        foreach ($vis as $i => $v)
                        {
                            ?>
                            <p style="text-align:center">-----</p>
                            <a href="profile.php?id=<?php echo $v['user_id']?>" style="color:white"><?php echo $v['username']?></a>
                            <p><?php echo $v['v_time']?></p>
                            <?php
                        }
                    ?>
                </div>
                <?php
                }
                else
                {
                    if($upcount != 0)
                    {
                    ?>
                    <div class="block" id="block" onclick="block();"></div>
                    <div class="rep" id="rep" onclick="report();"></div>
                    <?php
                    }
                }
                ?>
            </div>
           
        </div>
    </body>
    <script>
        var imgs = ["image list","<?php echo $info[0]['img1'] ?>","<?php echo $info[0]['img2'] ?>","<?php echo $info[0]['img3'] ?>","<?php echo $info[0]['img4'] ?>","<?php echo $info[0]['img5'] ?>"];
        var i = 1;

        function cycle() {
            if (i != 5)
                i += 1;
            else if (i == 5)
                i = 1;
            document.getElementById("img").src = imgs[i];
        };

        function like() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "likeywikey3.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id=<?php echo $_GET['id']?>");
        };

        function block() {
            var cnt = document.getElementById("like").innerHTML;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "blockywocky3.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id=<?php echo $_GET['id']?>");
            if (cnt == "unlike user")
                like();
        };

        function report() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "piemper3.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id=<?php echo $_GET['id']?>");
        };

        function buttons() {
            var xmlhttp = new XMLHttpRequest();
            <?php
            if($_SESSION['u_id'] != $_GET['id'] && $upcount != 0)
            {
                ?>
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("like").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "likeywikey2.php?id=<?php echo $_GET['id']?>", true);
                xmlhttp.send();

                xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("block").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "blockywocky2.php?id=<?php echo $_GET['id']?>", true);
                xmlhttp.send();

                xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("rep").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "piemper2.php?id=<?php echo $_GET['id']?>", true);
                xmlhttp.send();
            <?php
            }
            ?>

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tmp").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "matched.php?id=<?php echo $_GET['id']?>", true);
            xmlhttp.send();

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("status").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "status.php?id=<?php echo $_GET['id']?>", true);
            xmlhttp.send();
        };
        buttons();
        setInterval(buttons, 1000);
        setInterval(cycle, 30000);
    </script>
</html>