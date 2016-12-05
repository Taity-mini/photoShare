<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:17
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/photos.obj.php');
$conn = dbConnect();

if (!isset($_SESSION['userID'])) {
    header('Location:' . $domain);
    exit;
}


if (is_null($_GET["p"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $photos = new photos(htmlentities($_GET['p']));
    $photos->getAllDetails($conn);
    $photos->setUserID($_SESSION['userID']);

    if($photos->isPurchased($conn)){
        header('Location: ../message.php?id=nophoto');
        exit;
    }


    $groups = new user_groups();

    $groups->setUserID($_GET["p"]);
    $groups->getAllDetails($conn);




    if (!$photos->doesExist($conn)) {
        header('Location: ../message.php?id=nophoto');
        exit;
    }
}

if (isset($_POST['btnSubmit'])) {

    $conn = dbConnect();
    $photo = new photos(htmlentities($_GET['p']));
    $photo->getAllDetails($conn);
    $photo->setUserID($_SESSION['userID']);
    //Create user in the database

    if ($photos->purchase($conn)) {
        $_SESSION['purchase'] = true;

        header('Location: ../photos/view_photo.php?p=' . $photos->getPhotoID());
    } else {
        $_SESSION['purchaseError'] = true;
    }

}

//Go back to admin page
if (isset($_POST['btnBack'])) {
    header('Location: ../photos/view_photo.php?p=' . $photos->getPhotoID());
}
?>
<?php include('../inc/header.php'); ?>

<div class="grid-container">
    <?php
    $conn = dbConnect();

    $photos = new photos(htmlentities($_GET['p']));
    $photos->getAllDetails($conn);


    echo "<h1>Purchase PhotoID: " . $photos->getPhotoID() . "<h1>";
    ?>

    <form method="post">
        <?php

        echo "<img style='width:550px; height:550px;' src='" . $photos->getFilePath() . "' /></br>";
        echo '<label>Are you sure you want to purchase this photo for: £' . $photos->getPrice() . '?';
        echo '</br>';
        echo '<input type="submit" name="btnSubmit" value="Purchase Photo">';

        ?>
        </label>
        <input type="submit" name="btnBack" value="Go Back">
    </form>
</div>
<?php include('../inc/footer.php'); ?>
