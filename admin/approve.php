<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:21
 */
session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
$conn = dbConnect();
$access = false;
if (!isset($_SESSION['userID'])) {
    header('Location:' . $domain);
    exit;
}


if (is_null($_GET["u"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $users = new Users(htmlentities($_GET['u']));
    $groups = new user_groups();

    $groups->setUserID($_GET["u"]);
    $groups->getAllDetails($conn);


    if (isset($_SESSION['userID'])) {

        $users = new Users(htmlentities($_GET['u']));


        $userID = $_SESSION['userID'];
        $group = new user_groups();
        if ($group->isUserAdministrator($conn, $userID) && $_SESSION['userID'] !== $_GET["u"]) {
            $access = true;
        }
        else if(!$access){
            header('Location: ../message.php?id=badaccess');
        }
    }

    if (!$users->doesExist($conn)) {
        header('Location: ../message.php?id=nouser');
        exit;
    }
}

if (isset($_POST['btnSubmit'])) {

    //Get data from fields
    $conn = dbConnect();
    $user = new Users(htmlentities($_GET['u']));


    //Create user in the database

    if ($user->approveUser($conn)) {
        $_SESSION['approve'] = true;

        header('Location: ../admin/');
    } else {
        $_SESSION['approveError'] = true;
    }

}

//Go back to admin page
if (isset($_POST['btnBack'])) {
    header('Location: ../admin/');
}
?>
<?php include('../inc/header.php'); ?>

<div class="grid-container">
    <?php
    $conn = dbConnect();

    $users = new Users(htmlentities($_GET['u']));
    $users->getAllDetails($conn);
    $users->getUserNameFromUserID($conn);


    echo "<h1>Approve UserID: " . $users->getUserID() . "<h1>";
    ?>

    <form method="post">
        <?php

        echo '<label>Are you sure you want to approve: ' . $users->getUsername() . '?';
        echo '</br>';
        echo '<input type="submit" name="btnSubmit" value="Approve User">';

        ?>
        </label>
        <input type="submit" name="btnBack" value="Go Back">
    </form>
</div>
<?php include('../inc/footer.php'); ?>
