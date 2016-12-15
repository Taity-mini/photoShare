<?php
//Photoshare
//Albums JSON API
session_start();
header('Content-Type: application/json');
include('../inc/config.php');
require_once('../obj/albums.obj.php');

$conn = dbConnect();

$albums = new albums();
$albumID = "";

//Get all albums
if (!isset($_GET["a"])) {
    $albums->listAllAlbumsAPI($conn);

}
//Get albums based on albumID
else if(isset($_GET["a"])){
    $albumID = htmlentities($_GET["a"]);
    $albums->listAllAlbumsAPI($conn, $albumID);
}

dbClose();

