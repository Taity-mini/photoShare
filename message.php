<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 03/12/2016
 * Time: 22:16
 * Message php for various error/access denied prompts
 */

session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');

$conn = dbConnect();

?>

<?php include('inc/header.php'); ?>
    <div class="grid-container">
        <?php
        if ($_GET['id'] == 'badaccess') {
            echo '<h1>Access Denied</h1>
                <p>You do not have the required permissions to access that area.</p>
                <p>You could try:</p>
                <ul>';

            if (isset($_SESSION['userID'])) {
                echo '<li><a href="login.php">Logging in</a> to the website</li>';
            }

            echo '<li>Returning to the <a href="../index.php">homepage</a></li>
                </ul>';
        }

        if ($_GET['id'] == 'approval') {
            echo "<h1>Access Denied</h1>
                <p>You're account is needs to approved first before using the site</p>
                <p>You could try:</p>
                <ul>";

            if (isset($_SESSION['userID'])) {
                echo '<li><a href="login.php">Logging in</a> to the website</li>';
            }

            echo '<li>Returning to the <a href="../index.php">homepage</a></li>
                </ul>';
        }

        if ($_GET['id'] == 'banned') {
            echo '<h1>Access Denied</h1>
                <p>You are currently banned</p>
                <p>You could try:</p>
                <ul>';

            if (!isset($_SESSION['userID'])) {
                echo '<li><a href="login.php">Logging in</a> to the website</li>';
            }
            echo '<li>Returning to the <a href="../index.php">homepage</a></li>
                </ul>';
        }

        //NO User
        if ($_GET['id'] == 'nouser') {
            echo '<h1>No User Found</h1>';

            echo "The requested user cannot be found on the system";
        }

        //NO Comment
        if ($_GET['id'] == 'noalbum') {
            echo '<h1>No Album Found</h1>';

            echo "The requested album cannot be found on the system";
        }

        //NO Photo
        if ($_GET['id'] == 'nophoto') {
            echo '<h1>No Photo Found</h1>';

            echo "The requested photo cannot be found on the system";
        }

        //NO Comment
        if ($_GET['id'] == 'nocomment') {
            echo '<h1>No Photo Found</h1>';

            echo "The requested comment cannot be found on the system";
        }

        ?>
    </div>

<?php include('inc/footer.php'); ?>