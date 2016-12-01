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
    <h1>PhotoShare</h1>
    <nav>
        <ul>
            <li><a href="../photos/">Photos</a>
                <?php
                if (isset($_SESSION['userID'])) {
                    echo '<ul>
                    <li><a href="../photos/upload.php">Upload</a></li>
                    <li><a href="../photos/create_album.php">Create Album</a></li>
                    <li><a href="../photos/purchase.php">Purchase</a></li>
                </ul>';
                }
                ?>
            </li>
            <?php
            if (isset($_SESSION['userID'])) {
                echo '<li><a href="../logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="../login.php">Login</a></li>';
                echo '<li><a href="../register.php">Registration</a></li>';
            }
            ?>
            <li><a href="#">Search</a></li>
            <?php

            //User Profile
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID'];
                echo '<li><a href="../profiles/view.php?u=' . $userID . '">Profile</a>
                <ul>
                    <li><a href="#">Update Profile</a></li>
                    </ul>
                </li>';
            }

            //Admin
            if (isset($_SESSION['userID'])) {
                echo '<li><a href="#">Admin</a>
                <ul>
                    <li><a href="#">Approve Member</a></li>
                    <li><a href="#">Banning</a></li>
                    </ul>
                </li>';
            }
            ?>
        </ul>
    </nav>
</header>