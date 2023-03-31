<?php

include_once '../config.php';

// create tables
mysqli_query($connect, "CREATE TABLE IF NOT EXISTS `user` (
    `row` int(200) AUTO_INCREMENT PRIMARY KEY,
    `from_id` varchar(20) COLLATE utf8mb4_bin NOT NULL,
    `step` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");

mysqli_query($connect, "CREATE TABLE IF NOT EXISTS `panels` (
    `row` int(200) AUTO_INCREMENT PRIMARY KEY,
    `login_link` varchar(50) COLLATE utf8mb4_bin NOT NULL,
    `username` varchar(50) COLLATE utf8mb4_bin NOT NULL,
    `password` varchar(50) COLLATE utf8mb4_bin NOT NULL,
    `domin` varchar(50) COLLATE utf8mb4_bin NOT NULL,
    `session` varchar(500) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");

mysqli_query($connect, "CREATE TABLE IF NOT EXISTS `send` (
    `send` varchar(50) PRIMARY KEY,
	`step` varchar(50) DEFAULT NULL,
	`user` INT(11) DEFAULT NULL,
	`type` varchar(50) DEFAULT NULL,
	`text` varchar(5000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");

// check if send table has rows
if(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM `send` WHERE `send` = 'no'")) == 0) {
    mysqli_query($connect, "INSERT INTO `send` (`send`) VALUES('no');");
}

// check database connection
if($connect->connect_error) {
    echo '<h1 style="text-align: center;margin-top:30px; color: red;">Failed to install database</h1>';
} else {
    echo '<h1 style="text-align: center;margin-top:30px; color: green">Database installed successfully</h1>';
}
