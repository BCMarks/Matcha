<?php
    session_start();

    include_once "config/setup.php";

    if (!isset($_POST["hidi"]) && !(isset($_FILES["pic"])))
    {
        header("Location: profile_edit.php?id=".$_SESSION['u_id']);
        exit();
    }

    $i = $_POST["hidi"];
    $pic = $_FILES["pic"]["tmp_name"];
    $id = $_SESSION["u_id"];
    $loc = "tmp/u".$id."_img".$i.".png";
    move_uploaded_file($pic, $loc);

    $prep = $con->prepare(" UPDATE images
                            SET img$i='$loc'
                            WHERE (img_uid=$id);");
    $prep->execute();

    header("Location: profile_edit.php?id=".$_SESSION['u_id']);
?>