<?php
//Banning and approval check

//Checks if on message page to avoid redirect loop
$url = $_SERVER["REQUEST_URI"];
$pos = strrpos($url, "message.php");

//If not on message page then carry out checks
if ($pos != true) {

    if (isset($_SESSION['userID'])) {
        $users = new Users(($_SESSION['userID']));
        if (!$users->isApproved($conn)) {
            header('Location: ../message.php?id=approval');
        }
        if ($users->isBanned($conn)) {
            header('Location: ../message.php?id=banned');
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
    <link rel="stylesheet" href="../css/unsemantic.min.css">

    <!--Custom CSS-->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/g/pure@0.6.0(tables-min.css+buttons-min.css+forms-min.css)">


    <!--Jquery Library-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="../inc/functions.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>

    <![endif]-->


    <script type="text/javascript" src="../highslide/highslide-with-gallery.js"></script>
    <link rel="stylesheet" type="text/css" href="../highslide/highslide.css"/>

    <!--
        2) Optionally override the settings defined at the top
        of the highslide.js file. The parameter hs.graphicsDir is important!
    -->

    <script type="text/javascript">
        hs.graphicsDir = '../highslide/graphics/';
        hs.align = 'center';
        hs.transitions = ['expand', 'crossfade'];
        hs.outlineType = 'rounded-white';
        hs.fadeInOut = true;
        //hs.dimmingOpacity = 0.75;

        // Add the controlbar
        hs.addSlideshow({
            //slideshowGroup: 'group1',
            interval: 5000,
            repeat: false,
            useControls: true,
            fixedControls: 'fit',
            overlayOptions: {
                opacity: .75,
                position: 'bottom center',
                hideOnMouseOut: true
            }
        });
    </script>
</head>
<body>
<header>
    <h1>PhotoShare</h1>
    <nav>
        <ul>
            <li><a href="../">Home</a></li>
            <li><a href="../photos/">Photos</a>
                <?php
                if (isset($_SESSION['userID'])) {
                    $userID = $_SESSION['userID'];
                    $group = new user_groups();
                    if ($group->isUserPhotographer($conn, $userID)) {
                        echo '<ul>
                    <li><a href="../photos/upload.php">Upload</a></li>
                    <li><a href="../photos/create_album.php">Create Album</a></li>
                </ul>';
                    }

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
            <li><a href="../search/">Search</a></li>
            <?php

            //User Profile
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID'];
                echo '<li><a href="../profiles/view.php?u=' . $userID . '">Profile</a>
                <ul>
                    <li><a href="../profiles/update.php?u=' . $userID . '">Update Profile</a></li>
                    </ul>
                </li>';
            }

            //Admin
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID'];
                $group = new user_groups();
                if ($group->isUserAdministrator($conn, $userID)) {
                    echo '<li><a href="../admin/">Admin</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
</header>