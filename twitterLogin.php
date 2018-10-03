<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 4/25/17
 * Time: 12:39 PM
 */
session_start();
header("Cache-Control: no-cache, must-revalidate");
include('functions_stuff.php');
include_once('header.php');

/** Redirect to twitterHome.php */
if (isset($_POST['reset'])) {
    ?>
    <!-- if the login is successful redirect to twitter home -->
    <script type="text/javascript">
        window.location.href = './twitterLogin.php';
    </script>
    <?php
}


/** Check the variables for database commands */
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

/** If the user has clicked the login button */
if (isset($_POST['login'])) {

    /**
     * Login
     */
    if((isset($_POST['username'])) && (isset($_POST['password'])) && isset($_POST['login']))
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

                ?>
                <!-- if the login is successful redirect to twitter home -->
                <script type="text/javascript">
                    window.location.href = './twitterHome.php';
                </script>
                <?php
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
}

/** If the user has signed up */
if (isset($_POST['signup'])) {
    if (isset($name) && isset($username) && isset($email) && isset($password)) {
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
                ?>
                <!-- if the login is successful redirect to twitter home -->
                <script type="text/javascript">
                    window.location.href = 'http://einstein.etsu.edu/~millerwt/labs/twitterHome.php';
                </script>
                <?php
            } else {
                die("That username already exists");
            }
        }

        close_db();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Login</title>
    <link type="text/css" rel="stylesheet" href="http://einstein.etsu.edu/~millerwt/labs/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="http://einstein.etsu.edu/~millerwt/labs/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<?php
if ((!isset($_POST['login']) && !isset($_POST['signupform'])) || isset($_POST['login'])) {
    ?>
    <div class="container" style="width: 40%;">
        <div class="jumbotron">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="uname">Username:</label>
                    <input class="form-control" type="text" id="uname" name="username">
                    <label for="pass">Password:</label>
                    <input class="form-control" type="password" id="pass" name="password"><br>
                    <input type="submit" class="btn btn-primary" value="Login" name="login">&nbsp;
                    <input type="submit" class="btn btn-success" name="signupform" value="Sign up">
                </div>
            </form>
        </div>
    </div>
    <?php
} else if (isset($_POST['signupform'])) {
    ?>
<div class="container" style="width: 40%;">
    <div class="jumbotron">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
                <label for="fname">Full Name:</label>
                <input class="form-control" type="text" id="fname" name="name">
                <label for="uname">Username:</label>
                <input class="form-control" type="text" id="uname" name="username">
                <label for="email">Email:</label>
                <input class="form-control" type="email" id="email" name="email">
                <label for="pass">Password:</label>
                <input class="form-control" type="password" id="pass" name="password"><br>
                <input type="submit" class="btn btn-success" value="Signup" name="signup">&nbsp;
                <input type="submit" class="btn btn-primary" name="reset" value="Login">
            </div>
        </form>
    </div>
</div>
<?php
}
?>
</body>
</html>

<!-- Load JS -->
<script type='text/javascript' src='bootstrap-3.3.7-dist/js/bootstrap.min.js'></script>