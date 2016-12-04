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
    $comments = new comments(htmlentities($_GET['c']));
    $comments->setCommentID(htmlentities($_GET['c']));
    $comments->getAllDetails($conn);

    if (!$comments->doesExist($conn)) {
        header('Location: ../message.php?id=nocomment');
        exit;
    }

    if(isset($_SESSION['userID']))
    {
        $userID = $_SESSION['userID'];
        $group = new user_groups();
        if ((($_SESSION['userID'] !==  $comments->getUserID())) && (!$group->isUserAdministrator($conn, $userID) || !$group->isUserPhotographer($conn, $userID))) {
            header('Location: ../message.php?id=badaccess');
        }
    } else{
        header('Location: ../message.php?id=badaccess');
    }

}

if (isset($_POST['btnSubmit'])) {

    $comment= new comments(htmlentities($_GET['c']));
    $comment->getAllDetails($conn);

    if ((isset($_POST['txtComment']))) {
        $comment->setComment(htmlentities($_POST['txtComment']));
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
    $comment= new comments(htmlentities($_GET['c']));
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


<?php include('../inc/header.php');?>

<div class="grid-container">


    <?php
    $conn = dbConnect();

    $users = new Users($photos->getUserID());
    $albums = new albums($photos->getAlbumID());
    $albums->getAllDetails($conn);
    $users->getAllDetails($conn);
    $users->getUserNameFromUserID($conn);

    $comments = new comments(htmlentities($_GET['c']));
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


<?php include('../inc/footer.php');?>