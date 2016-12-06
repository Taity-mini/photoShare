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
$conn = dbConnect();

if (!isset($_SESSION['userID'])) {
    header('Location:' . $domain);
    exit;
}
$access = false;



if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $group = new user_groups();
    if ($group->isUserAdministrator($conn, $userID)) {
        $access = true;
    }
    else if(!$access){
        header('Location: ../message.php?id=badaccess');
    }
}

?>

<?php include('../inc/header.php'); ?>

    <div class="grid-container">
        <h1>Administration Area</h1>
        <?php
        //Users
        $users = new users();
        $groups = new user_groups();
        $conn = dbConnect();
        $user_listing = $users->listAllUsers($conn);

        echo "<h2>Registered Users</h2>";

        if ($users->getTotalCount($conn) > 0 ) {
            echo '<table style="border 1px;">
        <tr>
            <th>Username<br></th>
            <th>Name</th>
            <th>Group</th>
            <th>Edit</th>
            <th>Approve</th>
            <th>Ban</th>
        </tr>';

            foreach ($user_listing  as $row) {
                $userName = new users($row['userID']);
                $userName->getAllDetails($conn);
                $group = new user_groups();
                $group->setUserID($userName->getUserID());
                $group->getAllDetails($conn);

                //Hyperlinks
                $userlink = "../profiles/view.php?u=" . $row['userID'];
                $editlink = "../profiles/update.php?u=" . $row['userID'];
                $approveLink = "../admin/approve.php?u=" . $row['userID'];
                $banLink = "../admin/banning.php?u=" . $row['userID'];

                echo '<td> <a href="' . $userlink . '">' . $userName->getUsername() . '</a></td>';
                echo '<td>' . $userName->getFirstName() . ' '.$userName->getLastName().'</td>';
                echo '<td>'.$group->getGroupName().'</td>';
                echo '<td><a href="' . $editlink . '">Edit</a></td>';
                if ((!$userName->isApproved($conn))) {
                    echo '<td><a href="' . $approveLink . '">Needs Approved</a></td>';
                } else {
                    echo '<td>Approved</td>';
                }
                if ((!$userName->isBanned($conn))) {
                    echo '<td><a href="' . $banLink . '">Ban</a></td>';
                } else {
                    echo '<td><a href="' . $banLink . '">UnBan</a></td>';
                }
                echo "<tr>";
            }
        } else {
            echo "No Users Registered";
        }
        echo "</table> </br>";
        ?>
    </div>
<?php include('../inc/footer.php'); ?>