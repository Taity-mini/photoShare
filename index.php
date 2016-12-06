<?php
session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');
require_once('obj/albums.obj.php');
require_once('obj/photos.obj.php');
require_once('obj/comments.obj.php');

$conn = dbConnect();
$user = "";
if (isset($_SESSION['userID'])) {

    $user = new users($_SESSION['username']);
    $user->setUserID($_SESSION['userID']);
    $user->getAllDetails($conn);
}

?>

<?php include('inc/header.php'); ?>
    <div class="grid-container">
        <?php
        $users = new users();
        $albums = new albums();
        $photos = new photos();
        $comments = new comments();

        if (isset($_SESSION['userID'])) {

            print "<h1 style='text-align: center'>Welcome to photoShare," . $user->getUsername() . "</h1>";
        }
        else{
            print "<h1 style='text-align: center'>Welcome to photoShare, Guest</h1>";
        }

        echo "<h2>Statistics</h2>";
        echo "On this site there are currently:
        <ul>
 
        <li><b>" . $users->getTotalCount($conn) . "</b> Registered users</li>
        <li><b>" . $albums->getTotalCount($conn) . "</b> Albums created </li>
        <li><b>" . $photos->getTotalCount($conn) . " </b> Photos uploaded</li>
        <li><b>" . $comments->getTotalCount($conn) . "</b> Comments submitted</li>
        </ul>";


        echo "<h2>Newest 5 Users</h2>";

        if ($user_listing = $users->getLatestFiveUsers($conn)) {
            echo "<ul>";
            foreach ($user_listing as $row) {
                $user = new users($row['userID']);
                $user->getAllDetails($conn);
                $userlink = "../profiles/view.php?u=" . $user->getUserID();
                echo '<li><b>Username:<a href="' . $userlink . '">' . $user->getUsername() . '</a></b></li>';
            }
            echo "</ul>";
        }


        echo "<h2>Latest 5 Albums</h2>";

        if ($album_listing = $albums->getLatestFiveAlbums($conn)) {

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
                echo '<br><a href="' . $albumlink . '"> <img style="width:250px; height:250px;"  src="' . $photos->getFilePath() . '"/></a>';
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

<?php include('inc/footer.php'); ?>