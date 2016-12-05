<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 05/12/2016
 * Time: 14:26
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/photos.obj.php');
$conn = dbConnect();


if (isset($_POST["perform"])) $_POST["perform"]();
function purchase() {
    $conn = dbConnect();
    $photo = new photos(htmlentities($_POST['id']));
    $photo->getAllDetails($conn);
    $photo->setUserID($_SESSION['userID']);
    //Create user in the database

    if ($photo->purchase($conn)) {
        $_SESSION['purchase'] = true;

        //header('Location: ../photos/view_photo.php?p=' . $photo->getPhotoID());
    } else {
        $_SESSION['purchaseError'] = true;
        //header('Location: ../photos/view_photo.php?p=' . $photo->getPhotoID());
    }
}