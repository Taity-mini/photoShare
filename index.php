<?php
session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');

$conn = dbConnect();
$user ="";
if (isset($_SESSION['userID'])) {

    $user = new users($_SESSION['username']);
    $user->setUserID($_SESSION['userID']);
    $user->getAllDetails($conn);

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
    <h1>PhotoShare</h1>
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
    <p>Testing</p>

    <?php
    if (isset($_SESSION['userID'])) {

        print $user->getUsername();

    }
    ?>

</div>

<footer>
    <span>© <?php echo date("Y"); ?> PhotoShare</span>
</footer>
</body>
</html>