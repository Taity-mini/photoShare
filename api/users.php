<?php

//Photoshare
//Users JSON API
session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');

$conn = dbConnect();

$users = new users();
$userID = "";

//Get all users
if (!isset($_GET["u"])) {
    $users->listAllUsersAPI($conn);

}
//Get user based on userID
else if(isset($_GET["u"])){
    $userID = htmlentities($_GET["u"]);
    $users->listAllUsersAPI($conn, $userID);

}

dbClose();

