<?php
session_start();

/** Checks to see if user is logged in */
if (!isset($_SESSION['username'])) {
    ?>
    <script>
        alert("You must login!");
        window.location.href = './twitterLogin.php';
    </script>
    <?php
}

include("functions_stuff.php");

/** get the current user's id */
$dbo = connect_db();

$statement = select_specific_user($dbo, $_SESSION['username']);
$user = $statement->fetch();
$this_userid = $user['ID'];

close_db();

/** Variable Checks (For initialization) */
if (!isset($_POST['name'])) {
    $name = null;
} elseif (isset($_POST['name'])) {
    $name = $_POST['name'];
}
if (!isset($_POST['username'])) {
    $username = null;
} elseif (isset($_POST['username'])) {
    $username = $_POST['username'];
}
if (!isset($_POST['email'])) {
    $email = null;
} elseif (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (!isset($_POST['password'])) {
    $password = null;
} elseif (isset($_POST['password'])) {
    $password = $_POST['password'];
}


/**
 * Sign in and log in code
 *
 *
 *
 **/
if (isset($name) && isset($username) && isset($email) && isset($password) && isset($_POST['signup'])) {
    $fullname = explode(' ', $name);
    $firstname = $fullname[0];
    $lastname = $fullname[1];

    $firstname = clean_data($firstname);
    $lastname = clean_data($lastname);
    $username = clean_data($_POST['username']);
    $email = clean_data($_POST['email']);
    $password = sha1(clean_data($_POST['password']));

    $dbo = connect_db();

    $statement = insert_new_user($dbo, $username, $firstname, $lastname, $email, $password);

    foreach ($statement as $row) {
        if ($username != $row[0]) {
            header ('Location: http://www.google.com');
        } else {
            die("That username already exists");
        }
    }

    close_db();

}

/**
 * Login
 */
if((isset($_POST['username'])) &&(isset($_POST['password'])) &&(isset($_POST['login'])))
{
    $dbo = connect_db();

    $username = clean_data($_POST['username']);
    $password = sha1(clean_data($_POST['password']));
    // Now, let's try to access the database table containing the users
    try
    {
        $statement = select_login_user($dbo, $username, $password);
        if ($statement -> rowCount() == 1)
        {
            $_SESSION['loggedin']=TRUE;
            // Get the user details from the SINGLE returned database row
            $row = $statement -> fetch();
            $_SESSION['userlevel'] = $row['ID'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['fname'] . " " . $row['lname'];
        }
        else{
            echo "<br><br><br>";
            echo("<h1>Invalid username or password.</h1>");
        }


        // Close the statement and the database
        close_db();
    }
    catch (Exception $error)
    {
        echo "Error occurred accessing user privileges: " . $error->getMessage();
    }
}

if (isset($_POST['logout'])) {
    clear_session();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bravo's Twitter Thing</title>
    <link type="text/css" rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<?php include_once('header.php'); ?>
<?php include_once('user_index.php'); ?>
<div class="container">
<?php

$dbo = connect_db();

if (isset($_POST['allUsers'])) {
    $statement = select_all_users($dbo);
    ?>
    <table class="table table-responsive">
    <thead>
        <td>username</td><td>email</td><td>follow/unfollow</td>
    </thead>
    <tbody>
    <?php
    foreach ($statement as $row) {
        $isFollowing = followOrUnfollow($dbo, $this_userid, $row['ID']);
        echo "<tr>";
        echo "<td><a href='./user_x.php/?username={$row['username']}'>".$row['username']."</a></td>";
        echo "<td><a href='mailto:{$row['email']}'>".$row['email']."</a></td>";
        echo $isFollowing;
        echo "</tr>";
    }
}
if (isset($_POST['following'])) {
$statement = get_following($dbo, $_SESSION['userlevel']);
?>
<table class="table table-responsive">
    <thead>
    <td>username</td><td>email</td>
    </thead>
    <tbody>
    <?php
    foreach ($statement as $row) {
      $isFollowing = followOrUnfollow($dbo, $this_userid, $row['ID']);
        echo "<tr>";
        echo "<td><a href='./user_x.php/?username={$row['username']}'>".$row['username']."</a></td>";
        echo "<td><a href='mailto:{$row['email']}'>".$row['email']."</a></td>";
        echo $isFollowing;
        echo "</tr>";
    }
}
  
  if (isset($_POST['myFollowers'])) {
$statement = get_followers($dbo, $_SESSION['userlevel']);
?>
<table class="table table-responsive">
    <thead>
    <td>username</td><td>email</td>
    </thead>
    <tbody>
    <?php
    foreach ($statement as $row){ 
      $isFollowing = followOrUnfollow($dbo, $this_userid, $row['ID']);
        echo "<tr>";
        echo "<td><a href='./user_x.php/?username={$row['username']}'>".$row['username']."</a></td>";
        echo "<td><a href='mailto:{$row['email']}'>".$row['email']."</a></td>";
        echo $isFollowing;
        echo "</tr>";
    }
}
?>
    </tbody>
    </table>
<?php
close_db();
?>
</div>

</body>
</html>

<!-- Load JS -->
<script type='text/javascript' src='./bootstrap-3.3.7-dist/js/bootstrap.min.js'></script>