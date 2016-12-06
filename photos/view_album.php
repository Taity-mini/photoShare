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
        header('Location: ../message.php?id=noalbum');
        exit;
    }


    if (isset($_POST['btnEdit'])) {
        header('Location: edit_album.php?u=' . $albums->getAlbumID());
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

        if(count($photo_listing) > 0){
            echo '<div class="highslide-gallery">';
            foreach ($photo_listing as $row) {
                $photolink = "../photos/view_photo.php?p=" . $row['photoID'];

                echo '<a href="' . $row['filePath'] . '" class="highslide" onclick="return hs.expand(this)">
                <img style="width:250px; height:250px;" src="' . $row['filePath'] . '" alt="Highslide JS"
                     title="Click to enlarge" />
            </a>';
                echo '<div class="highslide-caption">';
                echo 'Title: '.$row['title'] . '<br>';
                echo 'Description: '.$row['description'] . '<br>';
                echo '<b><a href="' . $photolink . '">View the Photo</a></b>';
                echo '</div>';

                if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                    echo '</br>';
                }
                $counter++;
            }
            echo "</div>";
        }else
        {
            echo "There are no photos in this album";
        }

        ?>

    </div>
<?php include('../inc/footer.php'); ?>