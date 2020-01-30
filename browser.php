<?php
    session_start();

    include "config/database.php";
    include "functions/user_functs.php";

    if(!isset($_SESSION['u_id']) || $_SESSION['u_id'] == "")
    {
        header('Location: sign-in.php');
        exit;
    }

    $u_id = $_SESSION["u_id"];
    $gend = get_data($_SESSION["username"], "gender");
    $pref = get_data($_SESSION["username"], "sex_pref");
    $loc = get_data($_SESSION["username"], "location");

    if($loc == NULL)
        header("Location: profile_edit.php?id=".$_SESSION["u_id"]);

    $prep = $con->prepare("SELECT blockee_id FROM blocked WHERE blocker_id='$u_id'");
    $prep->execute();
    $blocked = $prep->fetchAll(PDO::FETCH_ASSOC);

    $prep = $con->prepare("SELECT * FROM taglist");
    $prep->execute();
    $tags = $prep->fetchAll();

    function get_pp($user_id, $img)
    {
        include "config/database.php";

        $prep = $con->prepare(" SELECT img_uid, img1 FROM images WHERE img_uid='$user_id' ");
        $prep->execute();

        $data = $prep->fetch(PDO::FETCH_ASSOC);
        return ($data[$img]);
    }

?>
<script>

    function set_default() {
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

    function filt() {
        var xmlhttp = new XMLHttpRequest();

        var tag = document.getElementsByClassName("tag");
        var tout = [];
        var i = 0;
        while (i < 24)
        {
            if (tag[i].checked == true)
                tout.push(tag[i].value);
            i++;
        }

        var pass = "sort="+document.getElementById("sort").value+"&dir="+document.getElementById("dir").value;
        pass += "&a_min="+document.getElementById("age_min").value+"&a_max="+document.getElementById("age_max").value;
        pass += "&f_min="+document.getElementById("fame_min").value+"&f_max="+document.getElementById("fame_max").value;
        pass += "&loc="+document.getElementById("locat").value+"&tag="+JSON.stringify(tout);
        xmlhttp.open("POST", "browsywosy.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(pass);
        xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("content").innerHTML = this.responseText;
        }
    };
        
    };

//function for searching
</script>
<html>
    <link rel="stylesheet" href="css/home_style.css"/>
    <link rel="stylesheet" href="css/brows_style.css"/>

<body onload="set_default();filt();">
    <div class="bro_main">
        <?php include_once "head_foot.php"; ?>

        <div class="search">
        </div>

        <div class="search_filt">
            <div class="search">
            </div>

            <div class="filts">
                <form name="filtform" method="post" >
                    <div class="sort_sel">
                        <label>SORT BY: </label>
                        <select id="sort" name="sort">
                            <option value="age">AGE</option>
                            <option value="location">LOCATION</option>
                            <option value="fame">FAME</option>
                            <option value="tags">COMMON TAGS</option>
                        </select>
                        <select id="dir" name="dir">
                            <option value="ASC">Ascending order</option>
                            <option value="DESC">Descending order</option>
                        </select>
                    </div>
                    <div class="age_slides">
                        <span>Age: </span> <br>
                        <input class="my_slide" id="age_min" name="a_min" type="range" min="18" max="90" value="18">
                        <span>min: </span><span id="amin" ></span>
                        <input class="my_slide" id="age_max" name="a_max" type="range" min="18" max="90" value="90">
                        <span>max: </span><span id="amax" ></span>
                    </div>

                    <div class="fame_slides">
                        <span>Fame Rating: </span> <br>
                        <input class="fslide" name="f_min" id="fame_min" type="range" min="0" max="9" value="0">
                        <span>min: </span><span id="fmin" ></span>
                        <input class="fslide" name="f_max" id="fame_max" type="range" min="0" max="9" value="9">
                        <span>max: </span><span id="fmax" ></span>
                    </div>
                    
                    <div>
                        <span>Location: </span><span id="geo" ></span><br />
                        <input id="locat" name="loc" type="range" min="0" max="3" value="3">
                    </div>

                    <div class="interests">
                        <span><h3>Select interest with Tags: </h3></span>
                        
                            <?php foreach($tags as $l => $v){ ?>
                                <input class="tag" type="checkbox" name="tag[]" id="t<?php echo $v['tag_id']; ?>" value="<?php echo $v['tag_name']; ?>" /><?php echo $v['tag_name'];
                                if($v['tag_id'] % 4 == 0) { ?>
                                    <br>
                                <?php
                             } } ?>
                            
                    </div>
                </form>
                <button type="button" name="tag_submit" onclick="filt();">FILTER</button>

                <!-- ALLOW SORTING BY AGE(ASCENDING), LOCATION(ALPHABETICAL), FAME(ASCENDING), NUMBER OF COMMON TAGS(ASCENDING)-->
                <!-- ALSO MAYBE AJAX THE WHOLE THING -->
            </div>
        </div>

        <div class="content" id="content">
            
        </div>
    </div>
</body>

<script>

    var amin_slid = document.getElementById("age_min");
    var amax_slid = document.getElementById("age_max");
    var fmin_slid = document.getElementById("fame_min");
    var fmax_slid = document.getElementById("fame_max");
    var amin = document.getElementById("amin")
    var amax = document.getElementById("amax");
    var fmin = document.getElementById("fmin");
    var fmax = document.getElementById("fmax");

    var str = <?php echo json_encode($loc);?>;
    var area = str.split(", ");
    var locat = document.getElementById("locat");
    var geo = document.getElementById("geo");
   
    amin.innerHTML = amin_slid.value;
    amax.innerHTML = amax_slid.value;
    fmin.innerHTML = fmin_slid.value*100;
    fmax.innerHTML = fmax_slid.value*100;

    if(locat.value == 3)
        geo.innerHTML = "NO LIMIT";
    else
        geo.innerHTML = area[locat.value];

    locat.oninput = function() {
        if(locat.value == 3)
            geo.innerHTML = "NO LIMIT";
        else
            geo.innerHTML = area[locat.value];
    }

    amin_slid.oninput = function() {
        if (this.value > amax_slid.value)
        {
            this.value = amax_slid.value;
            amin.innerHTML = amax_slid.value;
        }
        else
            amin.innerHTML = this.value;
    }
    amax_slid.oninput = function() {
        if (this.value < amin_slid.value)
        {
            this.value = amin_slid.value;
            amax.innerHTML = amin_slid.value;
        }
        else
            amax.innerHTML = this.value;
    }

    fmin_slid.oninput = function() {
        if (this.value > fmax_slid.value)
        {
            this.value = fmax_slid.value;
            fmin.innerHTML = fmax_slid.value*100;
        }
        else
            fmin.innerHTML = this.value*100;
    }
    fmax_slid.oninput = function() {
        if (this.value < fmin_slid.value)
        {
            this.value = fmin_slid.value;
            fmax.innerHTML = fmin_slid.value*100;
        }
        else
            fmax.innerHTML = this.value*100;
    }
</script>

</html>