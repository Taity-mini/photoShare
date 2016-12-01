<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 29/11/2016
 * Time: 16:08
 */

session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
require_once('../obj/comments.obj.php');
$conn = dbConnect();

if (is_null($_GET["c"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {
    $users = new Users();
    $groups = new user_groups();
    $albums = new albums();
    $photos = new photos();
    $comments = new comments($_GET['c']);
    $comments->setCommentID($_GET['c']);
    $comments->getAllDetails($conn);



    if (!$comments->doesExist($conn)) {
        echo "Comment doesn't exist";
        exit;
    }
}

if (isset($_POST['btnSubmit'])) {

    $comment= new comments($_GET['c']);
    $comment->getAllDetails($conn);

    if ((isset($_POST['txtComment']))) {
        $comment->setComment($_POST['txtComment']);
        if ($comment->update($conn)) {
            $_SESSION['update'] = true;
            header('Location: view_photo.php?p='.$comment->getPhotoID());
        }
    } else {
        $_SESSION['error'] = true;
    }
}

//Delete Comment
if (isset($_POST['btnDelete'])) {
    $comment= new comments($_GET['c']);
    $comment->getAllDetails($conn);
    $photoID = $comment->getPhotoID();

    if ($comment->delete($conn)) {
        $_SESSION['delete'] = true;
        header('Location: view_photo.php?p='.$comment->getPhotoID());
    }
    else {
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
    <h1>PhotoShare | Edit Comment</h1>
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

    $users = new Users($photos->getUserID());
    $albums = new albums($photos->getAlbumID());
    $albums->getAllDetails($conn);
    $users->getAllDetails($conn);
    $users->getUserNameFromUserID($conn);

    $comments = new comments($_GET['c']);
    $comments->getAllDetails($conn);

    echo "<h1>Edit Comment ID: " . $comments->getCommentID()."<h1>";
    ?>

    <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post" style="text-align: center">
        <textarea rows="4" cols="50" name = "txtComment" id = "txtComment"><?php echo $comments->getComment();?></textarea>
        <br>
        <input type="submit" name="btnSubmit" value="Update Comment">
        <input type="submit" name="btnDelete" value="Delete Comment" onclick="return confirm('Are you sure?')">
    </form>

</div>

<footer>
    <span>© <?php echo date("Y"); ?> PhotoShare</span>
</footer>
</body>
</html>