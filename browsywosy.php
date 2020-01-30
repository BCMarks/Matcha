<?php
    session_start();

    include "config/database.php";
    include "functions/user_functs.php";

    $u_id = $_SESSION["u_id"];
    $gend = get_data($_SESSION["username"], "gender");
    $pref = get_data($_SESSION["username"], "sex_pref");
    $loc = get_data($_SESSION["username"], "location");

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

    function loc_match($u_loc, $match_loc, $loc_filt)
    {
        $m_locs = explode(", ", $match_loc);
        $u_locs = explode(", ", $u_loc);

        $i = $loc_filt;
        while ($i < 3)
        {
            if ($m_locs[$i] != $u_locs[$i])
                return (0);
            $i++;
        }
        return (1);
    }

    if (isset($_POST['sort']) && isset($_POST['dir']))
    {
        $type = $_POST['sort'];
        $dir = $_POST['dir'];

        if($type == "tags")
        {
            $prep = $con->prepare(" SELECT * FROM users JOIN tags ON users.user_id = tags.tag_uid WHERE user_id!='$u_id' AND active=1 AND location IS NOT NULL AND
                                gender IS NOT NULL AND sex_pref IS NOT NULL AND bio IS NOT NULL AND age IS NOT NULL ORDER BY fame DESC");
            $prep->execute();
            $matches = $prep->fetchAll(PDO::FETCH_ASSOC);
            //check for common tags and resort

        }
        else
        {
            $prep = $con->prepare(" SELECT * FROM users WHERE user_id!='$u_id' AND active=1 AND location IS NOT NULL AND
                                gender IS NOT NULL AND sex_pref IS NOT NULL AND bio IS NOT NULL AND age IS NOT NULL ORDER BY $type $dir");
            $prep->execute();
            $matches = $prep->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    else
    {
        $prep = $con->prepare(" SELECT * FROM users WHERE user_id!='$u_id' AND active=1 AND location IS NOT NULL AND
                                gender IS NOT NULL AND sex_pref IS NOT NULL AND bio IS NOT NULL AND age IS NOT NULL ORDER BY fame DESC");
        $prep->execute();
        $matches = $prep->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $i = 0;
    $new_matches = array();
    $len = count($matches);
    while ($i < $len)
    {
        $pro_gen = $matches[$i]["gender"];
        $pro_id = $matches[$i]["user_id"];

        $j = 0;
        $b_len = count($blocked);
        $is_block = 0;
        while ($j < $b_len)
        {
            if ($blocked[$j]["blockee_id"] == $pro_id)
                $is_block = 1;
            $j++;
        }

        if (!$is_block && get_pp($pro_id, "img1") != "tmp/no_photo.png" /*&& $matches[$i]['location'] == $loc*/) 
        {
            if ($pref == "Other")
                array_push($new_matches, $matches[$i]);
            else if ($pref == "Bisexual" && ($pro_gen == "Female" || $pro_gen == "Male"))
                array_push($new_matches, $matches[$i]);
            else if ($pref == $pro_gen)
                array_push($new_matches, $matches[$i]);
        }
        $i++;
    }

    $matches = $new_matches;
    $i = 0;
    $m_len = count($matches);
    $tag_ids = array();
    $matches_points = array();


    if (isset($_POST["a_min"]) && isset($_POST["a_max"]) && isset($_POST["f_min"]) && isset($_POST["f_max"]) && isset($_POST["loc"]))
    {

        if(isset($_POST["tag"]))
            $tag_sel = json_decode($_POST["tag"]);
        else
            $tag_sel = array();

        $t_len = count($tag_sel);

        $i = 0;
        while ($i < $t_len)
        {
            $tag = $tag_sel[$i];
            $j = 1;

            $prep = $con->prepare("SELECT tag_id FROM taglist WHERE tag_name='$tag'");
            $prep->execute();
            $tag_id = $prep->fetch(PDO::FETCH_ASSOC);

            array_push($tag_ids, $tag_id["tag_id"]);

            $i++;
        }

        $m = 0;
        while ($m < $m_len) 
        {
            $m_id = $matches[$m]["user_id"];
            $points = 0;

            $tid_len = count($tag_ids);
            $i = 0;

            while ($i < $tid_len)
            {
                $a_tag = $tag_ids[$i];
                
                $prep = $con->prepare("SELECT tag$a_tag FROM tags WHERE tag_uid='$m_id'");
                $prep->execute();
                $tag_val = $prep->fetch(PDO::FETCH_BOTH);
                $tag_val = $tag_val[0];
                
                if ($tag_val == 1)
                    $points++;
                $i++;
            }
            if ($tid_len > 0)
                array_push($matches_points, $points);
            $m++;
        }

        $new_matches = array();
        $i = 0;
        while ($i < $m_len)
        {

            $match_point = 0;

            if (count($matches_points) == 0)
                $match_point = 1;
            else
                $match_point = $matches_points[$i];
            if ($match_point > 0 && $matches[$i]["age"] >= $_POST["a_min"] && $matches[$i]["age"] <= $_POST["a_max"] &&
                $matches[$i]["fame"] >= $_POST["f_min"]*100 && $matches[$i]["fame"] <= $_POST["f_max"]*100 && loc_match($matches[$i]["location"], $loc, $_POST["loc"]))
                array_push($new_matches, $matches[$i]);

            $i++;
        }
    }
    $matches = $new_matches;
    $m_len = count($matches);

    if (isset($_POST["sort"]) && isset($_POST["dir"]))
    {
        $new_matches = array();

        if ($_POST["sort"] == "tags")
        {
            $prep = $con->prepare("SELECT * FROM tags WHERE tag_uid=:uidg;");
            $prep->bindParam(':uidg', $_SESSION['u_id']);
            $prep->execute();
            $u_tags = $prep->fetchAll();
            $u_tags = $u_tags[0];

            $new_u_tags = array();
            $i = 1;
            while ($i < 25)
            {
                if ($u_tags["tag$i"] == 1)
                    array_push($new_u_tags, $u_tags["tag$i"]);
                $i++;
            }
            $u_tags = $new_u_tags;
        }
    }

    $html = "";
    $i = 0;
    while ($i < $m_len)
    { 
        $prof_page = "profile.php?id=".$matches[$i]["user_id"];
        $pp = get_pp($matches[$i]["user_id"], "img1");
        $nem = $matches[$i]["username"];
        if ($pp != "tmp/no_photo.png")
        {
            $html .= "<div class=\"a_prof\" onclick=\"window.open('$prof_page', '_blank')\"><img src=\"$pp\" /><span>$nem</span></div>";
        }
        $i++;
    }
    if ($html == "")
        $html = "<center>THERE ARE NO USERS THAT MATCH YOUR SEARCH CRITERIA.</center>";
    echo $html;
?>