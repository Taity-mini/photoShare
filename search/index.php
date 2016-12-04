<?php
/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 01/11/2016
 * Time: 15:20
 */


session_start();

include('../inc/config.php');
require_once('../obj/users.obj.php');
require_once('../obj/users.groups.obj.php');
require_once('../obj/albums.obj.php');
require_once('../obj/photos.obj.php');
require_once('../obj/comments.obj.php');

$conn = dbConnect();

//listing variables
//$user_listing = null;
$album_listing = null;
$photo_listing = null;


//Search users
if (isset($_POST['btnSubmitUsers'])) {

    if (isset($_POST['query'])) {
        $users = new users();
        $field = $_POST['field'];
        $query = $_POST['query'];
        if ($field == "username") {
            $users->setUsername($query);
            if ($users->getUserIDFromUsername($conn)) {
                $query = $users->getUserID();
                $field = 'userID';
                if ($user_listing = $users->search($conn, $field, $query)) {
                    $_SESSION['searchUsers'] = true;
                }
            } else {
                $_SESSION['searchFail'] = true;
                exit;
            }
        } else {
            if ($user_listing = $users->search($conn, $field, $query)) {
                $_SESSION['searchUsers'] = true;
            } else {
                $_SESSION['searchFail'] = true;
            }
        }

    } else {
        $_SESSION['error'] = true;
    }
}

//Search Photos
if (isset($_POST['btnSubmitPhotos'])) {

    if (isset($_POST['query'])) {
        $photos = new photos();
        $users = new users();
        $field = $_POST['field'];
        $query = $_POST['query'];

        if ($field == "username") {
            $users->setUsername($query);
            if ($users->getUserIDFromUsername($conn)) {
                $query = $users->getUserID();
                $field = 'userID';
                if ($photo_listing = $photos->search($conn, $field, $query)) {
                    $_SESSION['searchPhotos'] = true;
                }
            } else {
                $_SESSION['searchFail'] = true;
                exit;
            }
        } else {
            if ($photo_listing = $photos->search($conn, $field, $query)) {

                $_SESSION['searchPhotos'] = true;
            } else {
                $_SESSION['searchFail'] = true;
            }
        }

    } else {
        $_SESSION['error'] = true;
    }
}

if (isset($_POST['btnSubmitAlbums'])) {

    if (isset($_POST['query'])) {
        $albums = new albums();
        $users = new users();
        $field = $_POST['field'];
        $query = $_POST['query'];

        if ($field == "username") {
            $users->setUsername($query);
            if ($users->getUserIDFromUsername($conn)) {
                $query = $users->getUserID();
                $field = 'userID';
                if ($album_listing = $albums->search($conn, $field, $query)) {
                    $_SESSION['searchAlbums'] = true;
                }
            } else {
                $_SESSION['searchFail'] = true;
                exit;
            }
        } else {
            if ($album_listing = $albums->search($conn, $field, $query)) {

                $_SESSION['searchAlbums'] = true;
            } else {
                $_SESSION['searchFail'] = true;
            }
        }

    } else {
        $_SESSION['error'] = true;
    }
}

?>

<?php include('../inc/header.php'); ?>
    <div class="grid-container">
        <?php
        $users = new users();
        $albums = new albums();
        $photos = new photos();
        $comments = new comments();


        echo " <h1>Search</h1>";

        //Search switch buttons
        echo '<div id ="search" style="display:block"><input type="button" name="answer" value="Search Users" onclick="toggle_visibility_2(\'searchUsers\');" />';
        echo '<input type="button" name="answer" value="Search Photos" onclick="toggle_visibility_2(\'searchPhotos\')" />';
        echo '<input type="button" name="answer" value="Search Albums" onclick="toggle_visibility_2(\'searchAlbums\')" /></div>';


        if (isset($_SESSION['error'])) {
            echo "Form incomplete, errors are highlighted bellow";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['searchFail'])) {
            echo "Search Failed";
            unset($_SESSION['searchFail']);
        }
        ?>

        <div id="searchUsers" style="display:none;">
            <h2>Search Users</h2>
            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post"
                  style="text-align: center">
                Search for where: <Select name="field">
                    <Option value="username">Username</option>
                    <Option value="firstName">First Name</option>
                    <Option value="LastName">Last Name</option>
                    <Option value="bio">Bio</option>
                    <Option value="website">Website</option>
                </Select>
                = <input type="text" name="query"/>
                <input type="submit" name="btnSubmitUsers" value="Search"/>
            </form>
        </div>

        <div id="searchPhotos" style="display:none;">
            <h2>Search Photos</h2>
            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post"
                  style="text-align: center">
                Search for where: <Select name="field">
                    <Option value="username">Username</option>
                    <Option value="title">Title</option>
                    <Option value="description">description</option>
                    <Option value="price">price</option>
                </Select>
                = <input type="text" name="query"/>
                <input type="submit" name="btnSubmitPhotos" value="Search"/>
            </form>
        </div>

        <div id="searchAlbums" style="display:none;">
            <h2>Search Albums</h2>
            <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post"
                  style="text-align: center">
                Search for where: <Select name="field">
                    <Option value="username">Username</option>
                    <Option value="albumName">Title</option>
                    <Option value="albumDescription">description</option>
                </Select>
                = <input type="text" name="query"/>
                <input type="submit" name="btnSubmitAlbums" value="Search"/>
            </form>
        </div>


        <?php
        //Search Users Results
        if (isset($_SESSION['searchUsers'])) {
            unset($_SESSION['searchUsers']);
            echo "<h2>Search User Results</h2>";
            echo "<h3>Results Returned:" . count($user_listing) . "</h3>";
            echo "<ul>";
            print "
        <table border='1' cellspacing='0'>
            <tr>
                <td>User ID</td>
                <td>Username</td>
                <td>Group</td>
                <td>First Name</td>
                <td>Last Name</td>
                <td>Bio</td>
                <td>Website</td>
            </tr>";
            foreach ($user_listing as $info) {
                $user = new users($info['userID']);
                $user->getAllDetails($conn);
                $userlink = "../profiles/view.php?u=" . $user->getUserID();

                $groups = new user_groups();
                $groups->setUserID($user->getUserID());
                $groups->getAllDetails($conn);

                echo "<trstyle='background-color:#000000;'>";
                echo "<td>" . $user->getUserID() . "</td>";
                echo '<td> <a href="' . $userlink . '">' . $user->getUsername() . '</a></td>';
                echo "<td>" . $groups->getGroupName() . "</td>";
                echo "<td>" . $user->getFirstName() . "</td>";
                echo "<td>" . $user->getLastName() . "</td>";
                echo "<td>" . $user->getBio() . "</td>";
                echo '<td><a href="' . $user->getWebsite() . '">View</a></td>';
                echo "</tr>";
            }
            echo "</table>";
        }

        //Photo search results
        if (isset($_SESSION['searchPhotos'])) {
            unset($_SESSION['searchPhotos']);
            echo "<h2>Search Photos Results</h2>";
            echo "<h3>Results Returned:" . count($photo_listing) . "</h3>";
            $cols = 4;    // Define number of columns
            $counter = 1;     // Counter used to identify if we need to start or end a row

            echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
            foreach ($photo_listing as $row) {
                $user = new users($row['userID']);
                $user->getAllDetails($conn);
                if (($counter % $cols) == 1) {    // Check if it's new row
                    echo '<tr>';
                }
                $userlink = "../profiles/view.php?u=" . $user->getUserID();
                $photolink = "../photos/view_photo.php?p=" . $row['photoID'];
                echo "<td><b>Title:" . $row['title'] . "</b><br>";
                echo '<b>Username:<a href="' . $userlink . '">' . $user->getUsername() . '</a></b>';
                echo '<br><a href="' . $photolink . '"> <img style="width:250px; height:250px;"  src="' . $row['filePath'] . '"/></a>';
                echo "</td>";
                if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                    echo '</tr>';
                }
                $counter++;
            }
            echo "</table>";
        }


        //Search albums results

        if (isset($_SESSION['searchAlbums'])) {
            unset($_SESSION['searchAlbums']);
            echo "<h2>Search Album Results</h2>";
            echo "<h3>Results Returned:" . count($album_listing) . "</h3>";
            $cols = 4;    // Define number of columns
            $counter = 1;     // Counter used to identify if we need to start or end a row
            $photos = new Photos();

            echo '<table width="100%" align="center" cellpadding="4" cellspacing="1">';
            foreach ($album_listing as $row) {
                $photos->setAlbumID($row['albumID']);
                $photos->getLatestPhoto($conn);
                $user = new users($row['userID']);
                $user->getAllDetails($conn);

                if (($counter % $cols) == 1) {    // Check if it's new row
                    echo '<tr>';
                }
                $albumlink = "../photos/view_album.php?u=" . $row['albumID'];
                $userlink = "../profiles/view.php?u=" . $user->getUserID();
                echo "<td><b>Title: " . $row['albumName'] . "</b><br>";
                echo '<b>Username:<a href="' . $userlink . '">' . $user->getUsername() . '</a></b>';
                echo '<br><a href="' . $albumlink . '"> <img style="width:350px; height:350px;"  src="' . $photos->getFilePath() . '"/></a>';
                echo "</td>";
                if (($counter % $cols) == 0) { // If it's last column in each row then counter remainder will be zero
                    echo '</tr>';
                }
                $counter++;
            }
            echo "</table>";
        }
        ?>
    </div>

<?php include('../inc/footer.php'); ?>