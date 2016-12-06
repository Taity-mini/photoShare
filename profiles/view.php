<?php
session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/photos.obj.php');
require_once('../obj/albums.obj.php');
$conn = dbConnect();

if (is_null($_GET["u"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $users = new Users($_GET["u"]);
    $groups = new user_groups();

    if (!$users->doesExist($conn)) {
        header('Location: ../message.php?id=nouser');
        exit;
    }
}

?>

<?php include('../inc/header.php'); ?>

    <div class="grid-container">
        <h1>User Profile</h1>

        <?php
        $conn = dbConnect();
        $users = new Users($_GET["u"]);
        $users->getAllDetails($conn);
        $users->getUserNameFromUserID($conn);
        $groups = new user_groups();

        $groups->setUserID($_GET["u"]);
        $groups->getAllDetails($conn);

        echo "Username: " . $users->getUsername();
        echo "</br>";
        echo "First Name " . $users->getFirstName();
        echo "</br>";
        echo "Last Name " . $users->getLastName();
        echo "</br>";

        echo "Email:  " . $users->getEmail();
        echo "</br>";
        if($users->getWebsite() == ""){
            echo "Website:  <a href=". $users->getWebsite().">View here</a>";
            echo "</br>";
        }

        echo "Bio:  " . $users->getBio();
        echo "</br>";


        echo "Group: " . $groups->getGroupName();


        if ($groups->isUserPhotographer($conn, $users->getUserID())) {

            $albums = new albums();


            $album_listing = $albums->listAllAlbums($conn, $users->getUserID());

            echo "<h2>Albums [" . count($album_listing) . "]</h2>";

            if (count($album_listing) > 0) {

                $cols = 5;    // Define number of columns
                $counter = 1;     // Counter used to identify if we need to start or end a row
                $photos = new Photos();

                echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
                foreach ($album_listing as $row) {
                    $photos->setAlbumID($row['albumID']);
                    $photos->getLatestPhoto($conn);

                    if (($counter % $cols) == 1) {    // Check if it's new row
                        echo '<tr>';
                    }
                    $albumlink = "../photos/view_album.php?u=" . $row['albumID'];
                    echo "<td><b>Title:" . $row['albumName'] . "</b>";
                    echo '<br><a href="' . $albumlink . '"> <img style="width:250px; height:250px;"  src="' . $photos->getFilePath() . '"/></a>';
                    echo "</td>";
                    if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                        echo '</tr>';
                    }
                    $counter++;
                }
                echo "</table>";
            } else {
                echo "This user doesn't have any albums, at the moment";
            }
        }

        if ($groups->isUserShopper($conn, $users->getUserID())) {
            $photos = new Photos();
            $purchase_listing = $photos->listPurchases($conn, $users->getUserID());
            echo "<h2>Purchases [" . count($purchase_listing) . "]</h2>";


            $cols = 4;    // Define number of columns
            $counter = 1;     // Counter used to identify if we need to start or end a row

            if (count($purchase_listing) > 0) {
                echo '<div class="highslide-gallery">';
                foreach ($purchase_listing as $row) {
                    $photo = new photos($row['photoID']);
                    $photo->getAllDetails($conn);
                    $photolink = "../photos/view_photo.php?p=" . $row['photoID'];

                    echo '<a href="' . $photo->getFilePath() . '" class="highslide" onclick="return hs.expand(this)">
                <img style="width:250px; height:250px;" src="' . $photo->getFilePath() . '" alt="Highslide JS"
                     title="Click to enlarge" />
            </a>';
                    echo '<div class="highslide-caption">';
                    echo "<td><b>Title:" . $photo->getTitle() . "</b></br>";
                    echo "<b>Price £:" . $photo->getPrice() . "</b></br>";
                    echo '<b><a href="' . $photolink . '">View the Photo</a></b>';
                    echo '</div>';

                    if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                        echo '</br>';
                    }
                    $counter++;
                }
                echo "</div>";
                echo "</div>";
            } else {
                echo "This user hasn't purchased any photos yet.";
            }
        }
        ?>
    </div>
<?php include('../inc/footer.php'); ?>