<?php
    session_start();

    include_once "config/database.php";
    include_once "config/setup.php";

    $sql = 'SELECT * FROM taglist';
    $sth = $con->prepare($sql);
    $sth->execute();
    $tags = $sth->fetchAll();

    if(count($tags) == 0)
    {
        $h = "#";
        $sql = 'INSERT INTO taglist(tag_name) VALUES("'.$h.'love"),("'.$h.'fashion"),("'.$h.'art"),("'.$h.'nature"),("'.$h.'music"),("'.$h.'food"),("'.$h.'books"),("'.$h.'fitness"),("'.$h.'swag"),("'.$h.'hashtag"),("'.$h.'sports"),("'.$h.'party"),("'.$h.'animals"),("'.$h.'religion"),("'.$h.'movies"),("'.$h.'anime"),("'.$h.'kpop"),("'.$h.'activist"),("'.$h.'games"),("'.$h.'money"),("'.$h.'horror"),("'.$h.'comedy"),("'.$h.'fun"),("'.$h.'technology")';
        $sth = $con->prepare($sql);
        $sth->execute();
    }

?>
<html>
    <link rel="stylesheet" href="css/home_style.css"/>

    <body>
        <div class="main">
            <?php include_once "head_foot.php"; ?>

            <div class="cont">
                <?php
                if(isset($_SESSION['username']))
                {
                    ?>
                    <h1>Hello there <?php echo $_SESSION['username']?>, welcome to Matcha, where you will meet "The One" you have always been looking for.</h1>
                    <?php
                }
                else
                {
                ?>
                    <h1>Hello there, welcome to Matcha, where you will meet "The One" you have always been looking for.</h1>
                <?php
                }
                ?>
                <img class="couple" src="couple.jpeg" />
            </div>
        <div>
    </body>
</html>