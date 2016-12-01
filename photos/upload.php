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

if (isset($_POST['btnSubmit'])) {




    $user = new users($_SESSION['userID']);
    $user->getAllDetails($conn);
    $albums = new albums();
    $photos = new photos();

    if (isset($_POST['txtTitle']) && (isset($_POST['txtDescription']))&& (isset($_POST['txtPrice']))) {


        $photos->setAlbumID($_POST['sltAlbum']);
        $photos->setUserID($user->getUserID());
        $photos->setTitle($_POST['txtTitle']);
        $photos->setDescription($_POST['txtDescription']);
        $photos->setPrice($_POST['txtPrice']);


        if ($photos->uploadPhoto()) {
            if($photos->create($conn)) {
                $_SESSION['create'] = true;
            }
        }
    } else {
        $_SESSION['error'] = true;
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
    <h1>PhotoShare | Create Album</h1>
    <nav>
        <ul>
            <li><a href "#">Photos</a>
                <ul>
                    <li><a href "#">Upload</a></li>
                    <li><a href "#">Create Album</a></li>
                    <li><a href "#">Purchase</a></li>
                </ul>
            </li>
            <li><a href "#">Login</a></li>
            <li><a href "#">Registration</a></li>
            <li><a href="#">Search</a></li>
            <li><a href "#">Admin</a>
                <ul>
                    <li><a href="#">Approve Member</a></li>
                    <li><a href="#">Banning</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<!--Content-->
<div class="grid-container">
    <p>Upload new photo</p>

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

        <input type="submit" name="btnSubmit" value="Create Album">
    </form>
</div>
<footer>
    <span>© <?php echo date("Y"); ?> PhotoShare</span>
</footer>
</body>
</html>
