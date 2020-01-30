<?php
    session_start();
    include_once "functions/user_functs.php";
    include_once "config/database.php";

    if (!isset($_SESSION['u_id']) || !isset($_GET['id']) || ($_GET['id'] != $_SESSION['u_id']))
    {
        header('Location: index.php');
        exit();
    }

    $u_id = $_SESSION["u_id"];
    $error = "";
    $url = "profile_edit.php?id=".$u_id;
    
    $prep = $con->prepare("SELECT img_uid, img1, img2, img3, img4, img5 FROM images WHERE img_uid='$u_id'");
    $prep->execute();

    $imgs = $prep->fetch(PDO::FETCH_BOTH);

    $prep = $con->prepare("SELECT * FROM taglist");
    $prep->execute();
    $tags = $prep->fetchAll();

    $u_id = $_SESSION["u_id"];

    if (isset($_POST["username"]) && $_POST["username"] != "")
        $error = update_user($u_id, 'username', $_POST["username"]);
    if (isset($_POST["first_name"]) && $_POST["first_name"] != "")
        $error = update_user($u_id, 'first_name', $_POST["first_name"]);
    if (isset($_POST["last_name"]) && $_POST["last_name"] != "")
        $error = update_user($u_id, 'last_name', $_POST["last_name"]);
    if (isset($_POST["email"]) && $_POST["email"] != "")
        $error = update_user($u_id, 'email', $_POST["email"]);
    if (isset($_POST["password"]) && $_POST["password"] != "")
    {
        $special_pass = preg_match('@[^\w]@', $_POST["password"]);
        if ($special_pass == 0)
            $error = "Password must contain a special character.";
        else 
            $error = update_user($u_id, 'password', $_POST["password"]);

    }
    if (isset($_POST["gender"]) && $_POST["gender"] != "")
        $error = update_user($u_id, 'gender', $_POST["gender"]);
    if (isset($_POST["sex_pref"]) && $_POST["sex_pref"]!= "")
        $error = update_user($u_id, 'sex_pref', $_POST["sex_pref"]);
    if (isset($_POST["age"]) && $_POST["age"] != "")  
    {
        if ($_POST["age"] <= 0 || $_POST["age"] >= 120)
            $error = "Come on! Put in a realistic age. Asshole.";
        else
            $error = update_user($u_id, 'age', $_POST["age"]);
    }

    if (isset($_POST["bio"]) && $_POST["bio"] != "")
        $error = update_user($u_id, 'bio', $_POST["bio"]);

    if (isset($_POST["loc"]))
        $error = update_user($u_id, 'location', $_POST["loc"]);

    if (isset($_POST["btag"]))
    {
        $prep = $con->prepare("UPDATE tags SET tag1 = 0, tag2 = 0, tag3 = 0, tag4 = 0, tag5 = 0, tag6 = 0, tag7 = 0, tag8 = 0, tag9 = 0, tag10 = 0, tag11 = 0, tag12 = 0, tag13 = 0, tag14 = 0, tag15 = 0, tag16 = 0, tag17 = 0, tag18 = 0, tag19 = 0, tag20 = 0, tag21 = 0, tag22 = 0, tag23 = 0, tag24 = 0 WHERE tag_uid=:uidg;");
        $prep->bindParam(':uidg', $_SESSION['u_id']);
        $prep->execute();
        
        if (isset($_POST["tag"]))
        {
            foreach($_POST["tag"] as $j => $w)
            {
                foreach($tags as $i => $v)
                {
                    if($v['tag_name'] == $w)
                    {
                        $x = $v['tag_id'];
                        $prep = $con->prepare("UPDATE tags SET tag$x = 1 WHERE tag_uid=:uidg;");
                        $prep->bindParam(':uidg', $_SESSION['u_id']);
                        $prep->execute();
                    }
                }
            }
        }

        foreach($tags as $i => $v)
        {
            $x = $v['tag_id'];
            $prep = $con->prepare("SELECT count(tag$x) FROM tags WHERE tag$x = 1;");
            $prep->execute();
            $pop = $prep->fetchAll();
            $pop = $pop[0][0];

            $prep = $con->prepare("UPDATE taglist SET pop = $pop WHERE tag_id=$x;");
            $prep->execute();
        }
    }

    $sql = 'SELECT * FROM users WHERE `user_id`=:usid';
    $sth = $con->prepare($sql);
    $sth->bindParam(':usid', $u_id);
    $sth->execute();
    $uinfo = $sth->fetchAll();    

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script language="JavaScript" src="http://www.geoplugin.net/javascript.gp" type="text/javascript"></script>
<script>
    var imgs = ["image list","<?php echo $imgs[1] ?>","<?php echo $imgs[2] ?>","<?php echo $imgs[3] ?>","<?php echo $imgs[4] ?>","<?php echo $imgs[5] ?>"];
    var i = 1;

    function set_i() {
        document.getElementById("hidi").value = i;
    }

    function set_h() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
        }
    }

    function successFunction(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
        
            $.getJSON('https://nominatim.openstreetmap.org/reverse', {
            lat: lat,
            lon: lng,
            format: 'json',
        }, function (result) {
            document.getElementById("hidh").value = result.address.city + ", " + result.address.state + ", " + result.address.country;
        }); 
        }
        
        function errorFunction(){
            document.getElementById("hidh").value = geoplugin_city()+", "+geoplugin_region()+", "+geoplugin_countryName();
        }

    function next() {
        if (i != 5)
            i += 1;
        else if (i == 5)
            i = 1;
        document.getElementById("pp").src = imgs[i];
        document.getElementById("i").innerHTML = i;
        set_i();
    }

    function prev() {
        if (i != 1)
            i -= 1;
        else if (i == 1)
            i = 5;
        document.getElementById("pp").src = imgs[i];
        document.getElementById("i").innerHTML = i;
        set_i();
    }

    function set_default() {
        document.getElementById("bio").value = <?php echo json_encode($uinfo[0]["bio"]); ?>;
        document.getElementById("v1").value = <?php echo json_encode($uinfo[0]["gender"]); ?>;
        document.getElementById("v2").value = <?php echo json_encode($uinfo[0]["sex_pref"]); ?>;

        <?php
        $prep = $con->prepare("SELECT * FROM tags WHERE tag_uid=:uidg;");
        $prep->bindParam(':uidg', $_SESSION['u_id']);
        $prep->execute();
        $stag = $prep->fetchAll();

        if (count($stag) != 0)
        {
            $count = 1;
            while ($count < 25)
            {
                if($stag[0]["tag$count"] != 0)
                {
                    ?>
                    document.getElementById("t<?php echo $count?>").setAttribute("checked","checked");
                    <?php
                }
                $count++;
            }
        }
        ?>
    }
</script>

<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/proedit_style.css"/>

    <body onload="set_default();">
        <div class="editmain">
            <?php include_once "head_foot.php"; ?>

            <form id="formo" class="edit_form" method="post" action=<?php echo $url; ?>>
                <span class="form_title"><h3>Edit Profile: </h3></span>
                <span class="error"><h2><?php echo $error; ?></h2></span>
                <label>Username: </label>
                <input type="text" name="username" value="<?php echo $uinfo[0]["username"]; ?>" autocomplete>
                <label>First Name: </label>
                <input type="text" name="first_name" value="<?php echo $uinfo[0]["first_name"]; ?>" autocomplete>
                <label>Last Name: </label>
                <input type="text" name="last_name" value="<?php echo $uinfo[0]["last_name"]; ?>" autocomplete>
                <label>Email: </label>
                <input type="email" name="email" value="<?php echo $uinfo[0]["email"]; ?>" autocomplete/>
                <label>Password: </label>
                <input type="password" name="password" minlength="8" value="" autocomplete/>
                <label>Gender: </label>
                <select id="v1" name="gender" form="formo">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <label>Sexual Preference: </label>
                <select id="v2" name="sex_pref" form="formo">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Bisexual">Bisexual</option>
                    <option value="Other">Other</option>
                </select>
                <label>Age: </label>
                <input type="number" name="age" min="18" max="90" value="<?php echo $uinfo[0]["age"]; ?>" autocomplete/>
                <input id="hidh" name="loc" type="hidden" value="<?php echo $_SESSION['uloc'];?>"/>
                <button type="button" id="loc" name="locbutt" onclick="set_h();">PROVIDE CURRENT LOCATION</button>
                <button type="submit" name="main_submit" ><h3>CONFIRM</h3></button>
            </form>

            <div class="pics">
                <div>
                    <form class="choose_form" action="change_pic.php" method="post" enctype="multipart/form-data">
                        <input id="pic" name="pic" type="file" accept="image/*" value="" onchange="document.getElementById('sub').removeAttribute('disabled');"/>
                        <input id="hidi" name="hidi" type="hidden" value="1"/>
                        <input id="sub" type="submit" value="Upload Image" name="submit" disabled/>
                    </form>
                </div>
                <div class="imgs">
                    <img id="pp" src="<?php echo $imgs[1]; ?>"/>
                </div>
                <div class="buttons">
                    <button class="prev" onclick="prev()">PREV</button>
                    <span style="margin: auto;"><span id="i">1</span>/5</span>
                    <button class="next" onclick="next()">NEXT</button>
                </div>
            </div>
            <div class="bio_tag">
                <div class="bio_edit">
                    <span><h2>Edit Your Bio</h2></span>
                    <form action="<?php echo $url; ?>" method="post" >
                        <textarea id="bio" name="bio" type="text"></textarea>
                        <button type="submit" name="submit" >CONFIRM</button>
                    </form>
                </div>
                <div class="tag_list">
                    <span><h3>Select interest with Tags: </h3></span>
                    <form class="tag_form" action="<?php echo $url; ?>" method="post" >
                        <?php foreach($tags as $i => $v){ ?>
                        <div><input type="checkbox" name="tag[]" id="t<?php echo $v['tag_id']; ?>" value="<?php echo $v['tag_name']; ?>" /><?php echo $v['tag_name']; ?> (<?php echo $v['pop'];?> <?php if($v['pop'] == 1){?>person is<?php }else{?>people are<?php } ?> interested in this...)</div>
                        <?php } ?>
                        <input type="hidden" name="btag" value="sneaky">
                        <button type="submit" name="tag_submit" >CONFIRM</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>