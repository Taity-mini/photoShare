<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 02/12/2016
 * Time: 17:31
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
require_once('../obj/comments.obj.php');

$conn = dbConnect();
if (is_null($_GET["p"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {

    $photos = new photos($_GET["p"]);


    if (!$photos->doesExist($conn)) {
        echo "Photo doesn't exist";
        exit;
    }

    if (isset($_POST['btnSubmit'])) {

        $photos = new photos(htmlentities($_GET['p']));
        $photos->getAllDetails($conn);

        if (isset($_POST['txtTitle']) && (isset($_POST['txtDescription'])) && (isset($_POST['txtPrice']))) {

            $photos->setTitle(htmlentities($_POST['txtTitle']));
            $photos->setDescription(htmlentities($_POST['txtDescription']));
            $photos->setPrice(htmlentities($_POST['txtPrice']));

                if ($photos->update($conn)) {
                    $_SESSION['upload'] = true;
                    header('Location: view_photo.php?p=' . $photos->getPhotoID());
                    die();
                }

        } else {
            $_SESSION['error'] = true;
        }
    }

    if (isset($_POST['btnDelete'])) {

        $photos = new photos(htmlentities($_GET['p']));
        $photos->getAllDetails($conn);
        $albumID = $photos->getAlbumID();
        $comment = new comments();

        //Delete all comments from the photo
        if ($comment->delete($conn, $photos->getPhotoID())) {
            //Delete photo from file system
            unlink($photos->getFilePath());
            //Finally delete photo
            if ($photos->delete($conn)) {
                $_SESSION['deletePhoto'] = true;
                header('Location: view_album.php?u=' . $albumID);
            }
        } else {
            $_SESSION['error'] = true;
        }
    }
}


?>
<?php include('../inc/header.php'); ?>
<!--Content-->
<div class="grid-container">


    <?php
    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted below";
        unset($_SESSION['error']);
    }

    $conn = dbConnect();

    $photos = new photos($_GET['p']);
    $photos->getAllDetails($conn);
    $user = new users($photos->getUserID());
    $user->getAllDetails($conn);
    $albums = new albums($photos->getAlbumID());
    $albums->getAllDetails($conn);

    echo '<h1>Edit Photo ID: ' . $photos->getPhotoID() . '</h1>';
    ?>
    <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post" style="text-align: center"
          enctype="multipart/form-data">
        <label>
            <span><b>Album:</b></span>
            <?php
            echo $albums->getAlbumName();
            ?>
        </label>
        <br/>
        <label>
            <span><b>Photo Title</b></span>
            <input type="text" id="txtTitle" name="txtTitle" maxlength="20" value="<?php echo $photos->getTitle(); ?>"/>
        </label>
        <br/>

        <label>
            <span><b>Photo Description</b></span>
            <textarea id="txtDescription" name="txtDescription" rows="2" maxlength="250"><?php echo $photos->getDescription(); ?></textarea>
        </label>
        <br/>

<!--        <label>-->
<!--            <span><b>Choose a photo</b></span>-->
<!--            <input type="file" name="fileToUpload" id="fileToUpload">-->
<!--        </label>-->
<!--        <br/>-->

        <label>
            <span><b>Set a price £:</b></span>
            <input type="text" id="txtPrice" name="txtPrice" maxlength="20" value="<?php echo $photos->getPrice(); ?>"/>
        </label>
        <br/>

        <input type="submit" name="btnSubmit" value="Update Photo">
        <input type="submit" name="btnDelete" value="Delete Photo"
               onclick="return confirm('Are you sure? This WILL delete this photo and comments for this photo')">
    </form>
</div>
<?php include('../inc/footer.php');?>