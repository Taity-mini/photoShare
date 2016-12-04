<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 03/12/2016
 * Time: 22:16
 */
session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');
require_once('obj/albums.obj.php');
require_once('obj/photos.obj.php');
require_once('obj/comments.obj.php');

$conn = dbConnect();
$user = "";
if (isset($_SESSION['userID'])) {

    $user = new users($_SESSION['username']);
    $user->setUserID($_SESSION['userID']);
    $user->getAllDetails($conn);
}

?>

<?php include('inc/header.php'); ?>
    <div class="grid-container">
        <h1>Page not Found</h1>
        <img src="https://http.cat/404"/>

    </div>

<?php include('inc/footer.php'); ?>