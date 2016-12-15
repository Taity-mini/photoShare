<?php

//Photoshare
//Photos JSON API
session_start();
header('Content-Type: application/json');
include('../inc/config.php');
require_once('../obj/photos.obj.php');

$conn = dbConnect();

$photos = new photos();
$photoID = "";

//Get all photos
if (!isset($_GET["p"])) {
    $photos->listAllPhotosAPI($conn);

}
//Get photo based on photoID
else if(isset($_GET["p"])){
    $photoID = htmlentities($_GET["p"]);
    $photos->listAllPhotosAPI($conn, $photoID);
}

dbClose();

