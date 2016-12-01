<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:13
 * Create album page
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');

$conn = dbConnect();

if (isset($_POST['btnSubmit'])) {

    $user = new users($_SESSION['userID']);
    $user->getAllDetails($conn);
    $albums = new albums();

    if (isset($_POST['txtName']) && (isset($_POST['txtDescription']))) {
        $albums->setAlbumName(htmlentities($_POST['txtName']));
        $albums->setAlbumDescription(htmlentities($_POST['txtDescription']));
        $albums->setUserID($user->getUserID());
        print($user->getUserID());

        if ($albums->create($conn)) {
            $_SESSION['create'] = true;
        }
    } else {
        $_SESSION['error'] = true;
    }
}
?>

<?php include('../inc/header.php');?>
<!--Content-->
<div class="grid-container">
    <p>Create new album</p>

    <?php
    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted bellow";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['create'])) {
        echo "Album successfully created, feel free to add another below:";
        unset($_SESSION['create']);
    }
    ?>
    <form action="" method="post">
        <label>
            <span><b>Album Name</b></span>
            <input type="text" id="txtName" name="txtName" maxlength="20"/>
        </label>
        <br/>

        <label>
            <span><b>Album Description</b></span>
            <textarea id="txtDescription" name="txtDescription" rows="4" maxlength="250"></textarea>
        </label>
        <br/>


        <input type="submit" name="btnSubmit" value="Create Album">
    </form>
</div>
<?php include('../inc/footer.php');?>




