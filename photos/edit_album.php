<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 02/12/2016
 * Time: 00:33
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
require_once('../obj/comments.obj.php');

$conn = dbConnect();

if (isset($_POST['btnSubmit'])) {

    $user = new users($_SESSION['userID']);
    $user->getAllDetails($conn);
    $albums = new albums($_GET["u"]);

    if (isset($_POST['txtName']) && (isset($_POST['txtDescription']))) {
        $albums->setAlbumName(htmlentities($_POST['txtName']));
        $albums->setAlbumDescription(htmlentities($_POST['txtDescription']));

        if ($albums->update($conn)) {
            $_SESSION['update'] = true;
            header('Location: view_album.php?u=' . $albums->getAlbumID());
        }
    } else {
        $_SESSION['error'] = true;
    }
}

if (isset($_POST['btnDelete'])) {

    $albums = new albums(htmlentities($_GET["u"]));
    $albums->getAllDetails($conn);
    $photos = new photos();
    $photos->setAlbumID($albums->getAlbumID());

    //Delete all comments on photos in the album
    if ($photo_listing = $photos->listPhotoAlbum($conn)) {
        foreach ($photo_listing as $row) {
            $comment = new comments();
            $comment->delete($conn, $row['photoID']);
        }
    } else {
        $_SESSION['error'] = true;
    }

    //Delete all photos from the album
    if ($photos->delete($conn, $albums->getAlbumID())) {
        //Finally delete album
        if ($albums->delete($conn)) {
            $_SESSION['delete'] = true;
            header('Location: ../photos/');
        }
    } else {
        $_SESSION['error'] = true;
    }
}

?>

<?php include('../inc/header.php'); ?>
    <!--Content-->
    <div class="grid-container">


        <?php
        $conn = dbConnect();

        if (isset($_SESSION['error'])) {
            echo "Form incomplete, errors are highlighted bellow";
            unset($_SESSION['error']);
        }

        $albums = new albums($_GET["u"]);
        $albums->getAllDetails($conn);

        echo '<h1>Edit album ID: ' . $albums->getAlbumID() . '</h1>';
        ?>
        <form action="" method="post">
            <label>
                <span><b>Album Name</b></span>
                <input type="text" id="txtName" name="txtName" maxlength="20"
                       value="<?php echo $albums->getAlbumName(); ?>"/>
            </label>
            <br/>
            <label>
                <span><b>Album Description</b></span>
                <textarea id="txtDescription" name="txtDescription" rows="4"
                          maxlength="250"><?php echo $albums->getAlbumDescription(); ?></textarea>
            </label>
            <br/>

            <input type="submit" name="btnSubmit" value="Update Album">
            <input type="submit" name="btnDelete" value="Delete Album"
                   onclick="return confirm('Are you sure? This WILL delete all photos and comments in the album.')">
        </form>
    </div>
<?php include('../inc/footer.php'); ?>