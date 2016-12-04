<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:20
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
$conn = dbConnect();


if (is_null($_GET["u"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $users = new Users(htmlentities($_GET['u']));
    $groups = new user_groups();

    $groups->setUserID($_GET["u"]);
    $groups->getAllDetails($conn);

    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID'];
        $group = new user_groups();
        if ((($_SESSION['userID'] !== $_GET["u"])) && (!$group->isUserAdministrator($conn, $userID) || !$group->isUserPhotographer($conn, $userID))) {
            header('Location: ../message.php?id=badaccess');
        }
    } else {
        header('Location: ../message.php?id=badaccess');
    }

    if (!$users->doesExist($conn)) {
        header('Location: ../message.php?id=nouser');
        exit;
    }
}

if (isset($_POST['btnSubmit'])) {

   //if (isset($_POST['txtUsername']) && (isset($_POST['txtWebsite'])) && (isset($_POST['txtEmail'])) && (isset($_POST['txtFirstName']))&& (isset($_POST['txtLastName']))) {
        //Get data from fields
        $user = new Users(htmlentities($_GET['u']));
        $user->setEmail($_POST['txtEmail']);
        $user->setFirstName($_POST['txtFirstName']);
        $user->setLastName($_POST['txtLastName']);
        $user->setBio($_POST['txtBio']);
        $user->setWebsite($_POST['txtWebsite']);
        var_dump($user);

        //Create user in the database

        if ($user->updateProfile($conn)) {
            $_SESSION['update'] = true;
            echo"Working";
            header('Location: ../profiles/view.php?u=' . $user->getUserID());
        } else {
            $_SESSION['error'] = true;
        }

}
?>


<?php include('../inc/header.php'); ?>

<div class="grid-container">


    <?php
    $conn = dbConnect();

    $users = new Users(htmlentities($_GET['u']));
    $users->getAllDetails($conn);
    $users->getUserNameFromUserID($conn);

    echo "<h1>Edit UserID: " . $users->getUserID() . "<h1>";

    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted bellow";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['update'])) {
        echo "Member updated successfully!";
        unset($_SESSION['update']);
    }

    ?>

    <form method="post">
        <label>
            <span><b>Username</b></span>
            <?php echo $users->getUsername(); ?>
        </label>
        <br/>

        <label>
            <span><b>Email</b></span>
            <input type="text" id="txtEmail" name="txtEmail" maxlength="100" value="<?php echo $users->getEmail(); ?>"/>
        </label>
        <br/>

        <label>
            <span><b>First Name</b></span>
            <input type="text" id="txtFirstName" name="txtFirstName" maxlength="20"
                   value="<?php echo $users->getFirstName(); ?>"/>
        </label>
        <br/>

        <label>
            <span><b>Last Name</b></span>
            <input type="text" id="txtLastName" name="txtLastName" maxlength="20"
                   value="<?php echo $users->getLastName(); ?>"/>
        </label>
        <br/>

        <label>
            <span><b>Bio</b></span>
            <textarea id="txtBio" name="txtBio" rows="8" maxlength="500"><?php echo $users->getBio(); ?></textarea>
        </label>
        <br/>

        <label>
            <span><b>Website</b></span>
            <input type="text" id="txtWebsite" name="txtWebsite" value="<?php echo $users->getWebsite(); ?>"
                   maxlength="500"/>
        </label>
        <br/>
        <input type="submit" name="btnSubmit" value="Update Profile">
    </form>
</div>
<?php include('../inc/footer.php'); ?>
