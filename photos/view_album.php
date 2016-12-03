<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:13
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
$conn = dbConnect();

if (is_null($_GET["u"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $users = new Users();
    $groups = new user_groups();
    $albums = new albums($_GET["u"]);

    if (!$albums->doesExist($conn)) {
        echo "album doesn't exist";
        exit;
    }

    if (isset($_POST['btnEdit'])) {
            header('Location: edit_album.php?u='.$albums->getAlbumID());
    }

}

?>

<?php include('../inc/header.php'); ?>

    <div class="grid-container">

        <?php
        $conn = dbConnect();
        $albums = new albums($_GET["u"]);
        $albums->getAllDetails($conn);

        $users = new Users($albums->getUserID());
        $users->getAllDetails($conn);
        $users->getUserNameFromUserID($conn);

        if (isset($_SESSION['update'])) {
            echo "Album successfully edited";
            unset($_SESSION['update']);
        }

        if (isset($_SESSION['upload'])) {
            echo "Photo successfully upload";
            unset($_SESSION['upload']);
        }

        echo '<h1>View Album -' . $albums->getAlbumName() . '</h1>';
        echo "Album Name: " . $albums->getAlbumName();
        echo "</br>";
        echo "Description: " . $albums->getAlbumDescription();
        echo "</br>";
        echo "Username: " . $users->getUsername();
        echo "</br>";


        ?>

        <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
            <input type="submit" name="btnEdit" value="Edit Album">
        </form>

        <?php
        echo "</br>";
        echo "<h2>Photos</h2>";

        $photos = new Photos();
        $photos->setAlbumID($albums->getAlbumID());
        $photo_listing = $photos->listPhotoAlbum($conn);


        $cols = 5;    // Define number of columns
        $counter = 1;     // Counter used to identify if we need to start or end a row

        echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
        foreach ($photo_listing as $row) {
            if (($counter % $cols) == 1) {    // Check if it's new row
                echo '<tr>';
            }
            $photolink = "../photos/view_photo.php?p=" . $row['photoID'];
            echo "<td><b>Title:" . $row['title'] . "</b>";
            echo '<br><a href="' . $photolink . '"> <img style="width:350px; height:350px;"  src="' . $row['filePath'] . '"/></a>';
            echo "</td>";
            if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                echo '</tr>';
            }
            $counter++;
        }
        echo "</table>";

        ?>

    </div>
<?php include('../inc/footer.php'); ?>