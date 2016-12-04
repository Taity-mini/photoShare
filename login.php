<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 20/11/2016
 * Time: 20:20
 */

session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');

$conn = dbConnect();

if (isset($_POST['btnSubmit'])) {


    if (empty($_POST['txtUsername']) || empty($_POST['txtPassword'])) {
        $_SESSION['error'] = true;
    } else {
        $user = new users();
        $user->setUsername($_POST['txtUsername']);
        $userID = $user->getUserIDFromUsername($conn);

        if ($user->Login($userID, $_POST['txtPassword'], $conn)) {
            $_SESSION['username'] = htmlentities($_POST['txtUsername']);
            $_SESSION['userID'] = $userID;

            $user->getAllDetails($conn);

            if (!$user->isApproved($conn)) {
                header('Location: message.php?id=approval');
            }
            if ($user->isBanned($conn)) {
                header('Location: message.php?id=banned');
            } else {
                header('Location: ' . $domain);
            }

        } else {
            $_SESSION['error'] = true;
        }
    }
}
?>

<?php include('inc/header.php'); ?>
    <!--Content-->
    <div class="grid-container">
        <p>Login</p>

        <?php
        if (isset($_SESSION['error'])) {
            echo "Form incomplete, errors are highlighted bellow";
            unset($_SESSION['error']);
        }
        ?>
        <form action="" method="post">
            <label>
                <span><b>Username</b></span>
                <input type="text" id="txtUsername" name="txtUsername" maxlength="20"/>
            </label>
            <br/>
            <label>
                <span><b>Password</b></span>
                <input type="password" id="txtPassword" name="txtPassword" maxlength="16"/>
            </label>
            <br/>

            <input type="submit" name="btnSubmit" value="Login">
        </form>
    </div>
<?php include('inc/footer.php'); ?>