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
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>PhotoShare</title>
    <meta name="description" content="PhotoShare">
    <!--Unsemantic framework CSS-->
    <link rel="stylesheet" href="../css/unsemantic.min.css">

    <!--Custom CSS-->
    <link rel="stylesheet" href="../css/style.css">

    <!--Jquery Library-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<header>
    <h1>PhotoShare | View Album</h1>
    <nav>
        <ul>
            <li><a href "#">Photos</a>
                <ul>
                    <li><a href="#">Upload</a></li>
                    <li><a href="#">Create Album</a></li>
                    <li><a href="#">Purchase</a></li>
                </ul>
            </li>
            <?php
            if (isset($_SESSION['userID'])) {
                echo '<li><a href="./logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="./login.php">Login</a></li>';
            }
            ?>

            <li><a href="#">Registration</a></li>
            <li><a href="#">Search</a></li>
            <li><a href="#">Admin</a>
                <ul>
                    <li><a href="#">Approve Member</a></li>
                    <li><a href="#">Banning</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<div class="grid-container">


    <?php
    $conn = dbConnect();
    $albums = new albums($_GET["u"]);
    $albums->getAllDetails($conn);

    $users = new Users($albums->getUserID());
    $users->getAllDetails($conn);
    $users->getUserNameFromUserID($conn);


    echo '<h1>View Album -' . $albums->getAlbumName() . '</h1>';
    echo "Album Name: " . $albums->getAlbumName();
    echo "</br>";
    echo "Description: " . $albums->getAlbumDescription();
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

<footer>
    <span>© <?php echo date("Y"); ?> PhotoShare</span>
</footer>
</body>
</html>