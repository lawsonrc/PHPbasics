<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 4/24/17
 * Time: 4:48 PM
 */
session_start();

/** Checks to see if user is logged in */
if (!isset($_SESSION['username'])) {
    ?>
    <script>
        alert("You must login!");
        window.location.href = '../twitterLogin.php';
    </script>
    <?php
}

/** Checks to see if the user being viewed is actually being passed as a GET variable */
if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    die("ERROR: incorrect username");
}

require ('functions_stuff.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username; ?></title>
    <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<?php
include ("header.php");
$dbo = connect_db();

$statement = select_specific_user($dbo, $username);
$user= $statement->fetch();
  close_db();
  $dbo = connect_db();
$statement2 = getUserTweets($dbo,$user['ID']);
?>
    <table>
      <tbody>
        <tr>
<?php
    $isFollowing = followOrUnfollow($dbo, $_SESSION['userlevel'], $user['ID']);
    echo $isFollowing;
?>    
        </tr>
    </tbody>
    </table>   
  <div class="container" style="width: 75%; background: lightblue; margin: 0 auto; text-align: center;">

<?php  
    displayTweets($dbo,$statement2);
?>
  </div>
<?php

close_db();
?>
</body>
 
<!-- Load JS -->
<script type='text/javascript' src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>