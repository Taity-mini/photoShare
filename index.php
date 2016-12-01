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

<?php include('inc/header.php');?>
<div class="grid-container">
    <p>Testing</p>

    <?php
    if (isset($_SESSION['userID'])) {

        print $user->getUsername();

    }
    ?>

</div>

<?php include('inc/footer.php');?>