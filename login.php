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

        $user = new users($_POST['txtUsername']);
        $userID = $user->getUserIDFromUsername($conn);

        if ($user->Login($userID, $_POST['txtPassword'], $conn)) {
            $_SESSION['username'] = $_POST['txtUsername'];
            $_SESSION['userID'] = $userID;
            header('Location: ' . $domain);
        } else {
            $_SESSION['error'] = true;
        }
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
    <link rel="stylesheet" href="css/unsemantic.min.css">

    <!--Custom CSS-->
    <link rel="stylesheet" href="css/style.css">

    <!--Jquery Library-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<header>
    <h1>PhotoShare | Login</h1>
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
<footer>
    <span>© <?php echo date("Y"); ?> PhotoShare</span>
</footer>
</body>
</html>