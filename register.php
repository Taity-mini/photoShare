<?php
session_start();

include('inc/config.php');
require_once('obj/users.obj.php');
require_once('obj/users.groups.obj.php');

$conn = dbConnect();

if (isset($_POST['btnSubmit'])) {

    $user = new users();
    $group = new user_groups();

    if (isset($_POST['txtUsername']) && (isset($_POST['txtPassword'])) && (isset($_POST['txtWebsite']))) {
        //Get username from field
        $user->setUsername($_POST['txtUsername']);
        if (!$user->doesUserNameExist($conn)) {

            $user->setEmail($_POST['txtEmail']);
            $user->setFirstName($_POST['txtFirstName']);
            $user->setLastName($_POST['txtLastName']);
            $user->setBio($_POST['txtBio']);
            $user->setWebsite($_POST['txtWebsite']);

            //Switch group based on checkbox value
            switch($_POST['chkGroup'])
            {
                case 0:
                    $group->setGroupID(3);
                    break;
                case 1:
                    $group->setGroupID(2);
                    break;
                default:
                    $group->setGroupID(3);
                    break;
            }
            //Create user in the database

            if($user->create($conn,$_POST['txtPassword'])) {
                $group->setUserID($user->getUserID());
                //Then set user group
                if($group->create($conn)){
                    $_SESSION['register'] = true;
                }
            }
        }
        else {
            $_SESSION['error'] = true;
        }
    } //Errors? Then display correct message
    else {
        $_SESSION['error'] = true;
    }
}
?>

<?php include('inc/header.php');?>
<!--Content-->
<div class="grid-container">
    <p>Registration</p>

    <?php
    if (isset($_SESSION['error'])) {
        echo "Form incomplete, errors are highlighted bellow";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['register'])) {
        echo "Member registered successfully, you can login now.";
        unset($_SESSION['register']);
    }
    ?>
    <form  action="" method="post">
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

        <label>
            <span><b>Email</b></span>
            <input type="text" id="txtEmail" name="txtEmail" maxlength="100"/>
        </label>
        <br/>

        <label>
            <span><b>First Name</b></span>
            <input type="text" id="txtFirstName" name="txtFirstName" maxlength="20"/>
        </label>
        <br/>

        <label>
            <span><b>Last Name</b></span>
            <input type="text" id="txtLastName" name="txtLastName" maxlength="20"/>
        </label>
        <br/>

        <label>
            <span><b>Bio</b></span>
            <textarea id="txtBio" name="txtBio" rows="8" maxlength="500"></textarea>
        </label>
        <br/>

        <label>
            <span><b>Website</b></span>
            <input type="text" id="txtWebsite" name="txtWebsite" maxlength="500"/>
        </label>
        <br/>

        <label>
            <span><b>Account Type</b></span>
            <p>Please <b>tick</b> the checkbox if you want a photographer account otherwise leave unselected for shopper account</p>
            <input type="hidden" name="chkGroup" value="0" />
            <input type="checkbox" name="chkGroup" value="1">
        </label>
        <br/>

        <input type="submit" name="btnSubmit" value="Register">
    </form>
</div>
<?php include('inc/footer.php');?>