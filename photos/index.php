<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 26/11/2016
 * Time: 17:57
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
$conn = dbConnect();


?>

<?php include('../inc/header.php'); ?>

    <div class="grid-container">

        <?php
        $conn = dbConnect();
        $albums = new albums();
        $photos = new Photos();
        $photos->setAlbumID($albums->getAlbumID());

        if (isset($_SESSION['delete'])) {
            echo "Album successfully deleted";
            unset($_SESSION['delete']);
        }

        echo "<h2>Latest 5 Photos</h2>";


        if ($photo_listing = $photos->getLatestFivePhotos($conn)) {

            $cols = 5;    // Define number of columns
            $counter = 1;     // Counter used to identify if we need to start or end a row

            echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
            foreach ($photo_listing as $row) {
                $user = new users($row['userID']);
                $user->getAllDetails($conn);
                if (($counter % $cols) == 1) {    // Check if it's new row
                    echo '<tr>';
                }
                $userlink = "../profiles/view.php?u=" . $user->getUserID();
                $photolink = "../photos/view_photo.php?p=" . $row['photoID'];
                echo "<td><b>Title:" . $row['title'] . "</b><br>";
                echo '<b>Username:<a href="' . $userlink . '">' . $user->getUsername() . '</a></b>';
                echo '<br><a href="' . $photolink . '"> <img style="width:250px; height:250px;"  src="' . $row['filePath'] . '"/></a>';
                echo "</td>";
                if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                    echo '</tr>';
                }
                $counter++;
            }
        }
        echo "</table>";


        echo "<h2>Albums</h2>";

        if ($album_listing = $albums->listAllAlbums($conn)) {

            $cols = 5;    // Define number of columns
            $counter = 1;     // Counter used to identify if we need to start or end a row
            $photos = new Photos();

            echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
            foreach ($album_listing as $row) {
                $photos->setAlbumID($row['albumID']);
                $photos->getLatestPhoto($conn);
                $user = new users($row['userID']);
                $user->getAllDetails($conn);

                if (($counter % $cols) == 1) {    // Check if it's new row
                    echo '<tr>';
                }
                $albumlink = "../photos/view_album.php?u=" . $row['albumID'];
                $userlink = "../profiles/view.php?u=" . $user->getUserID();
                echo "<td><b>Title: " . $row['albumName'] . "</b><br>";
                echo '<b>Username:<a href="' . $userlink . '">' . $user->getUsername() . '</a></b>';
                echo '<br><a href="' . $albumlink . '"> <img style="width:350px; height:350px;"  src="' . $photos->getFilePath() . '"/></a>';
                echo "</td>";
                if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                    echo '</tr>';
                }
                $counter++;
            }
            echo "</table>";
        } else {
            if (isset($_SESSION['userID'])) {
                echo "There isn't any albums, at the moment, please <a href ='../photos/create_album.php'>Create one</a>";
            } else {
                echo "There isn't any albums, at the moment";
            }
        }
        ?>
    </div>
<?php include('../inc/footer.php'); ?>