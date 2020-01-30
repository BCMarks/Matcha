<?php


try {
    include_once "database.php";

    $con = new PDO("mysql:host=localhost;dbname=matcha", "root", "colinear");
    $sql = "CREATE TABLE IF NOT EXISTS users (
                user_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                username VARCHAR(100) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                password TEXT NOT NULL,
                active BIT DEFAULT 0,
                gender ENUM ('Male','Female','Other'),
                sex_pref ENUM ('Male','Female','Bisexual','Other'),
                age INT,
                location TEXT,
                bio TEXT,
                fame INT DEFAULT 0,
                last_log DATETIME,
                avail INT DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS taglist (
                tag_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                tag_name TEXT,
                pop INT UNSIGNED DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS tags (
                tag_uid INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                tag1 BIT DEFAULT 0,
                tag2 BIT DEFAULT 0,
                tag3 BIT DEFAULT 0,
                tag4 BIT DEFAULT 0,
                tag5 BIT DEFAULT 0,
                tag6 BIT DEFAULT 0,
                tag7 BIT DEFAULT 0,
                tag8 BIT DEFAULT 0,
                tag9 BIT DEFAULT 0,
                tag10 BIT DEFAULT 0,
                tag11 BIT DEFAULT 0,
                tag12 BIT DEFAULT 0,
                tag13 BIT DEFAULT 0,
                tag14 BIT DEFAULT 0,
                tag15 BIT DEFAULT 0,
                tag16 BIT DEFAULT 0,
                tag17 BIT DEFAULT 0,
                tag18 BIT DEFAULT 0,
                tag19 BIT DEFAULT 0,
                tag20 BIT DEFAULT 0,
                tag21 BIT DEFAULT 0,
                tag22 BIT DEFAULT 0,
                tag23 BIT DEFAULT 0,
                tag24 BIT DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS images (
                img_uid INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                img1 TEXT,
                img2 TEXT,
                img3 TEXT,
                img4 TEXT,
                img5 TEXT
            );
            CREATE TABLE IF NOT EXISTS blocked (
                block_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                blocker_id INT UNSIGNED NOT NULL,
                blockee_id INT UNSIGNED NOT NULL
            );
            CREATE TABLE IF NOT EXISTS likes (
                like_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                liker_id INT UNSIGNED NOT NULL,
                likee_id INT UNSIGNED NOT NULL
            );
            CREATE TABLE IF NOT EXISTS visits (
                visit_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                visiter_id INT UNSIGNED NOT NULL,
                visitee_id INT UNSIGNED NOT NULL,
                v_time DATETIME NOT NULL
            );
            CREATE TABLE IF NOT EXISTS chat (
                chat_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                active BIT DEFAULT 1,
                u1_id INT UNSIGNED NOT NULL,
                u2_id INT UNSIGNED NOT NULL,
                paint TEXT NOT NULL,
                last_msg DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS msgs (
                msg_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                chat_id INT UNSIGNED NOT NULL,
                to_id INT UNSIGNED NOT NULL,
                from_id INT UNSIGNED NOT NULL,
                content TEXT,
                msg_time DATETIME
            );
            CREATE TABLE IF NOT EXISTS notif (
                n_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                n_uid INT UNSIGNED NOT NULL,
                n_cont TEXT,
                n_date DATETIME,
                n_read INT DEFAULT 0
            );
            CREATE TABLE IF NOT EXISTS reports (
                rep_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
                acc_id INT UNSIGNED NOT NULL,
                piemp_id INT UNSIGNED NOT NULL
            );
            ";
        
    $con->exec($sql);
    
}
catch (PDOexception $err) {
    echo $sql.PHP_EOL.$err->getMessage();
}

?>