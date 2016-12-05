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
require_once('../obj/comments.obj.php');
$conn = dbConnect();

if (is_null($_GET["p"])) {
    header('Location:' . $domain . '404.php');
    exit;
} else {

    $groups = new user_groups();
    $albums = new albums();
    $photos = new photos($_GET["p"]);


    if (!$photos->doesExist($conn)) {
        header('Location: ../message.php?id=nophoto');
        exit;
    }

    if (isset($_SESSION['userID'])) {
        $users = new Users(($_SESSION['userID']));

    }


    if (isset($_POST['btnEdit'])) {
        header('Location: edit_photo.php?p=' . $photos->getPhotoID());
    }

    if (isset($_POST['btnPurchase'])) {
        header('Location: purchase.php?p=' . $photos->getPhotoID());
    }


    if (isset($_POST['btnSubmit'])) {

        $user = new users($_SESSION['userID']);
        $user->getAllDetails($conn);
        $comment = new comments();


        if ((isset($_POST['txtComment']))) {
            $comment->setPhotoID($_GET['p']);
            $comment->setUserID($user->getUserID());
            $comment->setComment($_POST['txtComment']);

            if ($comment->create($conn)) {
                $_SESSION['create'] = true;
            }
        } else {
            $_SESSION['error'] = true;
        }
    }
}


?>
<?php include('../inc/header.php'); ?>

    <br class="grid-container">


        <?php
        $conn = dbConnect();
        $photos = new photos($_GET["p"]);
        $photos->getAllDetails($conn);


        $users = new Users($photos->getUserID());
        $albums = new albums($photos->getAlbumID());
        $albums->getAllDetails($conn);
        $users->getAllDetails($conn);
        $users->getUserNameFromUserID($conn);


        echo '<h1>View Photo -' . $photos->getTitle() . '</h1>';

        echo '<div class="highslide-gallery">';
        echo '<a  id="thumb1" href="' . $photos->getFilePath() . '" class="highslide" onclick="return hs.expand(this)">
                <img class="displayed" style="width:550px; height:550px" src="' . $photos->getFilePath() . '" alt="Highslide JS"
                     title="Click to enlarge" />
            </a>';

        echo '<div class="highslide-caption">';
        echo 'Title: '.$photos->getTitle() . '<br>';
        echo 'Description: '.$photos->getDescription() . '<br>';
        //echo '<b><a href="' . $photolink . '">View the Photo</a></b>';



        echo '</div>';
        echo '</div>';

        //echo "<img class='displayed' style='width:550px; height:550px;' src='" . $photos->getFilePath() . "' /></br>";
        //        echo "</br>";
        //        echo "Username:" . $users->getUsername();
        //        echo "</br>";
        //        echo "Album Name: " . $albums->getAlbumName();
        //        echo "</br>";
        //        echo "Description: " . $photos->getDescription();
        //        echo "</br>";
        //        echo "Price £: " . $photos->getPrice();
        //        echo "</br>";

        ?>

    </br>
        <table class="pure-table pure-table-bordered">
            <tr>
                <th>Username<br></th>
                <th><?php echo $users->getUsername(); ?><br></th>
            </tr>
            <tr>
                <td>Album Name<br></td>
                <td><?php echo $albums->getAlbumName(); ?></td>
            </tr>
            <tr>
                <td>Album Description</td>
                <td><?php echo $albums->getAlbumDescription(); ?></td>
            </tr>
            <tr>
                <td>Photo Description</td>
                <td><?php echo $photos->getDescription(); ?></td>
            </tr>
            <tr>
                <td>Price:</td>
                <td>£<?php echo $photos->getPrice(); ?></td>
            </tr>
            <tr>
                <td>Options</td>
                <td>
                    <?php
                    if (isset($_SESSION['userID'])) {
                        $userID = $_SESSION['userID'];
                        if ((($userID == $photos->getUserID()) && $group->isUserPhotographer($conn, $userID)) || ($group->isUserAdministrator($conn, $userID))) {

                            ?>
                            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
                                <input type="submit" name="btnEdit" value="Edit Photo">
                            </form>
                            <?php
                        }
                        $photos->setUserID($userID);
                        if ($photos->isPurchased($conn)) {
                            echo "<b>You have purchased this photo</b>";
                        } else if (((!$photos->isPurchased($conn)) && $group->isUserShopper($conn, $userID)) || ($group->isUserAdministrator($conn, $userID))) {

                            ?>

                            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
                                <input type="submit" name="btnPurchase" value="Purchase Photo">
                            </form>
                            <?php
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>


        <?php
        echo "<h2>EXIF Data</h2>";

        $photos->getExifData();
        //Photo data ENDS


        //Comment code s
        echo "<h2>Comments</h2>";
        $comments = new comments();

        $comments->setPhotoID($photos->getPhotoID());
        $comment_listing = $comments->listAllComments($conn);

        if ($comments->doesExist($conn)) {
            echo '<table style="width: 100%;" class="pure-table pure-table-bordered">
        <thead>
        <tr>
            <th>Username<br></th>
            <th>Comment</th>
            <th>Edit</th>
        </tr>
         </thead>';

            foreach ($comment_listing as $row) {
                $userName = new users($row['userID']);
                $userName->getAllDetails($conn);
                $userlink = "../profiles/view.php?u=" . $row['userID'];
                $editlink = "./edit_comment.php?c=" . $row['commentID'];

                echo '<td> <a href="' . $userlink . '">' . $userName->getUsername() . '</a></td>';
                echo '<td>' . $row["comment"] . '</td>';

                if ((isset($_SESSION['userID'])) && ($_SESSION['userID'] == $row['userID'])) {
                    echo '<td><a href="' . $editlink . '">Edit</a></td>';
                } else {
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                }
                echo "<tr>";
            }
        } else {
            echo "No comments available on this photo";
        }
        echo "</table> </br>";


        //If logged in then users can add comments
        if ((isset($_SESSION['userID']))) {
            if (isset($_SESSION['error'])) {
                echo "<br>Form incomplete, errors are highlighted bellow</b>";
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['create'])) {
                echo "<b>Comment successfully created, feel free to add another below:</b></br>";
                unset($_SESSION['create']);
                echo "</br>";
            }

            if (isset($_SESSION['update'])) {
                echo "<b>Comment successfully Updated</b></br>";
                unset($_SESSION['update']);

            }


            if (isset($_SESSION['delete'])) {
                echo "<b>Comment successfully deleted</b></br>";
                unset($_SESSION['delete']);
            }

            if (isset($_SESSION['photoUpdate'])) {
                echo "<b>Photo successfully Updated</b></br>";
                unset($_SESSION['photoUpdate']);

            }

            ?>
            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post"
                  style="text-align: center">
                <textarea rows="4" cols="50" name="txtComment" id="txtComment">Add your comment</textarea>
                </br>
                <input type="submit" name="btnSubmit" value="Add Comment">
            </form>
            <?php
        }
        ?>
    </div>
<?php include('../inc/footer.php'); ?>