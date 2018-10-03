<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 4/30/17
 * Time: 8:53 PM
 */
session_start();
/** Checks to see if the user being viewed is actually being passed as a GET variable */
if (isset($_GET['ID'])) {
    $otherUser = $_GET['ID'];
} else {
    die("ERROR: incorrect User ID");
}

include ("functions_stuff.php");

/** get the current user's id */
$dbo = connect_db();

$statement = select_specific_user($dbo, $_SESSION['username']);
$user = $statement->fetch();
$this_user = $user['ID'];

close_db();

$dbo = connect_db();

/** if the user isn't already following otherUser then follow */
if (followCheck($dbo, $this_user, $otherUser) == false) {
    try {
        set_following($dbo, $this_user, $otherUser);
        ?>
        <script type="text/javascript">
            window.location.href = 'javascript:history.back()\';
        </script>
        <?php
    } catch (PDOException $exception) {
        die ($exception->getMessage());
    }
    /** If the user is already following otherUser then unfollow */
} else if (followCheck($dbo, $this_user, $otherUser) == true) {
    try {
        set_unfollowing($dbo, $this_user, $otherUser);
        ?>
        <script type="text/javascript">
            window.location.href = 'javascript:history.back()\';
        </script>
        <?php
    } catch (PDOException $exception) {
        die ($exception->getMessage());
    }
}
close_db();
?>