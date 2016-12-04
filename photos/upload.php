<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:12
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');

$conn = dbConnect();

if(isset($_SESSION['userID']))
{
    $userID = $_SESSION['userID'];
    $group = new user_groups();
    if (!$group->isUserAdministrator($conn, $userID) || !$group->isUserPhotographer($conn, $userID)) {
        header('Location: ../message.php?id=badaccess');
    }
} else{
    header('Location: ../message.php?id=badaccess');
}


if (isset($_POST['btnSubmit'])) {


    $user = new users($_SESSION['userID']);
    $user->getAllDetails($conn);
    $albums = new albums();
    $photos = new photos();

    if (isset($_POST['txtTitle']) && (isset($_POST['txtDescription']))&& (isset($_POST['txtPrice']))) {


        $photos->setAlbumID(htmlentities($_POST['sltAlbum']));
        $photos->setUserID($user->getUserID());
        $photos->setTitle(htmlentities($_POST['txtTitle']));
        $photos->setDescription(htmlentities($_POST['txtDescription']));
        $photos->setPrice(htmlentities($_POST['txtPrice']));


        if ($photos->uploadPhoto()) {
            if($photos->create($conn)) {
                $_SESSION['upload'] = true;

                header('Location: view_album.php?u=' . $photos->getAlbumID());
                die();
            }
        }
    } else {
        $_SESSION['error'] = true;
    }
}

?>
<?php include('../inc/header.php');?>
<!--Content-->
<div class="grid-container">
    <h1>Upload new photo</h1>

    <?php
    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted bellow";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['upload'])) {
        echo "Album successfully created, feel free to add another below:";
        unset($_SESSION['upload']);
    }
    ?>
    <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post" style="text-align: center" enctype="multipart/form-data">
    <label>
            <span><b>Choose an album to upload to:</b></span>
            <?php
            $user = new users($_SESSION['userID']);
            $user->getAllDetails($conn);
            $albums = new albums();
            $options = $albums->listAllAlbumSelect($conn, $user->getUserID());
            //var_dump($options);
            echo '<select id="sltAlbum" name="sltAlbum">';
            foreach ($options as $key => $value) {

                echo '<option value="' . $key . '">' . $value . '</option>';

            }
            echo '</select>';
            ?>
        </label>
        <br/>
        <label>
            <span><b>Photo Title</b></span>
            <input type="text" id="txtTitle" name="txtTitle" maxlength="20"/>
        </label>
        <br/>

        <label>
            <span><b>Photo Description</b></span>
            <textarea id="txtDescription" name="txtDescription" rows="2" maxlength="250"></textarea>
        </label>
        <br/>

        <label>
            <span><b>Choose a photo</b></span>
            <input type="file" name="fileToUpload" id="fileToUpload">
        </label>
        <br/>

        <label>
            <span><b>Set a price £:</b></span>
            <input type="text" id="txtPrice" name="txtPrice" maxlength="20"/>
        </label>
        <br/>

        <input type="submit" name="btnSubmit" value="Upload Photo">
    </form>
</div>
<?php include('../inc/footer.php');?>