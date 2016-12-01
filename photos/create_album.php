<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:13
 * Create album page
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');

$conn = dbConnect();

if (isset($_POST['btnSubmit'])) {

    $user = new users($_SESSION['userID']);
    $user->getAllDetails($conn);
    $albums = new albums();

    if (isset($_POST['txtName']) && (isset($_POST['txtDescription']))) {
        $albums->setAlbumName($_POST['txtName']);
        $albums->setAlbumDescription($_POST['txtDescription']);
        $albums->setUserID($user->getUserID());
        print($user->getUserID());

        if ($albums->create($conn)) {
            $_SESSION['create'] = true;
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
    <p>Create new album</p>

    <?php
    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted bellow";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['create'])) {
        echo "Album successfully created, feel free to add another below:";
        unset($_SESSION['create']);
    }
    ?>
    <form action="" method="post">
        <label>
            <span><b>Album Name</b></span>
            <input type="text" id="txtName" name="txtName" maxlength="20"/>
        </label>
        <br/>

        <label>
            <span><b>Album Description</b></span>
            <textarea id="txtDescription" name="txtDescription" rows="4" maxlength="250"></textarea>
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




