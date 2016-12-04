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


    if(($_SESSION['userID'] == $_GET["u"]) && (!$groups->isUserAdministrator($conn,$users->getUserID()))){
        header('Location:' . $domain);
        exit;
    }

    if (!$users->doesExist($conn)) {
        header('Location: ../message.php?id=nouser');
        exit;
    }
}

if (isset($_POST['btnSubmit'])) {

      //Get data from fields
    $user = new Users(htmlentities($_GET['u']));


    //Create user in the database

    if ($user->banningToggleUser($conn, $users->getUserID())) {
        $_SESSION['ban'] = true;

        header('Location: ../admin/');
    } else {
        $_SESSION['banError'] = true;
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

    if(!$users->isBanned($conn)){
        echo "<h1>Ban UserID: " . $users->getUserID() . "<h1>";
    }
    else
    {
        echo "<h1>UnBan UserID: " . $users->getUserID() . "<h1>";
    }


    ?>

    <form method="post">
        <?php
        if(!$users->isBanned($conn)){
            echo '<label>Are you sure you want to ban '.$users->getUsername(). '?';
            echo '</br>';
            echo '<input type="submit" name="btnSubmit" value="Ban User">';
        }
        else{
            echo '<label>Are you sure you want to Unban '.$users->getUsername(). '?';
            echo '</br>';
            echo '<input type="submit" name="btnSubmit" value="Unban User">';
        }
        ?>

        </label>

        <input type="submit" name="btnBack" value="Go Back">
    </form>
</div>
<?php include('../inc/footer.php'); ?>

