<?php
/** This is the page that contains all the fucntions used within the project
    There is mainly database fucntion, but there is some other type of function within here
	This page was created by TEAM BRAVO that contains Austin Erskin, Jacob Horn, Bruce Raymond, Wilson Miller, Ryan Lawson
	*/


/** Connect to database */
function connect_db () {
    /* Database Connection details */
    $datasourcename = "mysql:host=localhost;dbname=millerwt";
    $dbusername = "millerwt";
    $dbpassword = "12345";
    /* Database check to see if the connection was successful */
    try {
        $dbo = new PDO($datasourcename, $dbusername, $dbpassword);
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    return $dbo;  // returns the dbo for easy dbo connections
}

/** Close database connection */
function close_db () {
    $dbo = null;
    $statement = null;
}

/** Cleans data for insertion into db */
function clean_data($var) {
    $var = trim($var);
    $var = htmlspecialchars($var);
    $var = stripslashes($var);
    return $var;
}

/** Clear the session variables (for sure) This is used for the logout process */
function clear_session() {
    unset($_SESSION);
    $_SESSION = array();
    session_destroy();
}


/**
 * MySQL Functions
 * This block of code will contain functions that will
 * allow us to pass variables directly to
 * the sql statements
 **/

/** Select all users from the database*/
function select_all_users($dbo) {
    $query = "SELECT ID, username, email FROM twitter_users ORDER BY ID;";
    $statement = $dbo->prepare($query);
    $statement->execute();

    return $statement;
}

/** Select info for user logging in */
function select_login_user ($dbo, $username, $password){
    $dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "SELECT * FROM twitter_users WHERE username = :user and password = :pw";
    $statement = $dbo -> prepare($query);
    $statement->bindparam(":user", $username);
    $statement->bindparam(":pw", $password);
    $statement -> execute();

    return $statement;
}

/** Insert new user into database when signing up */
function insert_new_user($dbo, $username, $firstname, $lastname, $email, $password) {
    $query = "INSERT INTO twitter_users (username, fname,lname, email, password) VALUES(:username, :fname, :lname, :email, :password);";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":username", $username);
    $statement->bindParam(":fname", $firstname);
    $statement->bindParam(":lname", $lastname);
    $statement->bindParam(":email", $email);
    $statement->bindParam(":password", $password);
    $statement->execute();

    //add a self-follow so user's tweets display in feed
    $query = "INSERT INTO twitter_followers(user1,user2) VALUES(:user,:user)";
    $statement2 = $dbo->prepare($query);
    $statement2->bindParam(":user",$_SESSION['userlevel']);
    $statement2->execute();
  
    return $statement;
}

/** Select specific user */
function select_specific_user($dbo, $username) {
    $query = "SELECT ID, username, email, fname, lname FROM twitter_users WHERE username = :username ORDER BY ID;";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":username", $username);
    $statement->execute();

    return $statement;
}

/** Insert new tweet into database */
function create_tweet($dbo, $tweet, $userid) {
    $query = "INSERT INTO twitter_tweets(tweet,twitter_user_id) values(:tweet,:userid)";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":userid", $userid);
    $statement->bindParam(":tweet", $tweet);
    $statement->execute();
}

/** Get all tweets where User 1 is following User 2 */
function getFollowerTweets($dbo,$username){
  $query = "SELECT * FROM twitter_tweets WHERE twitter_user_id IN (SELECT user_2 FROM twitter_follows WHERE user_1 = :id);";
  $statement = $dbo->prepare($query);
  $statement->bindParam(":id", $username);
  $statement->execute();
  
  return $statement;
}

/** Get single user's tweets */
function getUserTweets($dbo,$userid){
  $query = "SELECT * FROM twitter_tweets WHERE twitter_user_id = :id ORDER BY ID DESC;";
  $statement = $dbo->prepare($query);
  $statement->bindParam(":id", $userid);
  $statement->execute();
  
  return $statement;
}

/** Takes in tweets from another function and displays the likes and poster(Author of post) of each tweet */
function displayTweets($dbo, $tweets){
  /** foreach that goes through the array and retrieves the information of a tweet and author for the feed */
  foreach($tweets as $tweet){
    //get like information
    $query = "SELECT * FROM twitter_likes WHERE tweet_id = :tweetid;";
    $statement2 = $dbo->prepare($query);
    $statement2->bindParam(":tweetid",$tweet['ID']);
    $statement2->execute();

    //get tweet poster(author) information
    $query="SELECT username,fname,lname FROM twitter_users WHERE ID = :tweeterid;";
    $statement3 = $dbo->prepare($query);
    $statement3->bindParam(":tweeterid",$tweet['twitter_user_id']);
    $statement3->execute();
    $poster=$statement3->fetch();

    /**display tweets by the uses of html that is echoed*/
    echo '<br><div class="container" style="background: lightblue; width: 75%; margin: 0 auto;">'; 
    echo '<h2 style="text-align: left";>'.$poster['fname']." ".$poster['lname']."<br>@".$poster['username'].'</h2>';
    echo '<p class="well well-lg" style="width: 70%; height: 50%; margin: 0 auto; text-align: center; ">'.$tweet['tweet'].'</p>';
    echo '<p><form method="post" action="likeProcess.php">';
    echo '<input type="submit" class="btn btn-sm" value="';
	
	/** This is an if statement that check to see if the person has already liked 
		and display the html value corresponding to like or unlike*/
		
    if(likeCheck($dbo, $_SESSION['userlevel'], $tweet['ID']) == false){ 
      echo "Like";
    }
    else{ 
      echo "Unlike"; 
    }
    echo '" name="like">';
	/**This is static html that stores hidden information of the tweet for reference purposes */
    ?>
    <input type="hidden" name="tweetid" value="<?php echo $tweet['ID']?>" >
    <?php
	/* displays the amount of likes as a row count*/
    echo " {$statement2->rowCount()} Likes </form></p>";
    echo '<p>'.$tweet['time_entered'].'</p>';
    echo '</div>';
    }
}

/** creates a follower instance (Read as :"user_1 follows user_2) */
function set_following($dbo, $user1,$user2){
    $query = "INSERT INTO twitter_follows(user_1,user_2) VALUE(:user1,:user2);";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":user1", $user1);
    $statement->bindParam(":user2", $user2);
    $statement->execute();
}

/** deletes a follower instance (Read as :"user_1 follows user_2) */
function set_unfollowing($dbo,$user1,$user2){
    $query = "DELETE FROM twitter_follows WHERE user_1=:user1 AND user_2=:user2;";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":user1", $user1);
    $statement->bindParam(":user2", $user2);
    $statement->execute();
}
/** checks to see if the the following instance already exist*/
function followCheck($dbo,$user1,$user2)
{
    $follows = false;
    $query = "SELECT * FROM twitter_follows WHERE user_1=:user1 AND user_2=:user2;";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":user1", $user1);
    $statement->bindParam(":user2", $user2);
    $statement->execute();
    if ($statement->rowCount() >= 1) {
        $follows = true;
    }
    return $follows;
}

/** checks if user already liked a tweet **/
function likeCheck($dbo,$user1,$tweetid)
{
    $likes = false;
    $query = "SELECT * FROM twitter_likes WHERE user_id=:user1 AND tweet_id=:tweetid;";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":user1", $user1);
    $statement->bindParam(":tweetid", $tweetid);
    $statement->execute();
    if ($statement->rowCount() >= 1) {
        $likes = true;
    }
    return $likes;
}

/** Outputs 'Follow' or 'Unfollow' based on the followCheck function */
function followOrUnfollow($dbo, $user1, $user2) {
    if (followCheck($dbo, $user1, $user2) == false) {
        $result = "<td><a class='btn btn-primary' href='http://einstein.etsu.edu/~millerwt/Twitter/followProcess.php/?ID={$user2}'>Follow</a></td>";
    } else {
        $result = "<td><a class='btn btn-danger' href='http://einstein.etsu.edu/~millerwt/Twitter/followProcess.php/?ID={$user2}'>Unfollow</a></td>";
    }

    return $result;
}
/**
 * Show the list of the following for the user logged in
 * This function will require two variables ($dbo)a database connection,
 * ($userID)a follower(session_["userlevel"])
 */
function get_following ($dbo,$userID){
    $query = "select twitter_users.username, twitter_users.ID, twitter_users.email 
from twitter_follows
inner join twitter_users 
on twitter_users.ID = twitter_follows.user_2
where user_1=:username
and twitter_follows.user_1 NOT IN (
  select twitter_users.ID FROM twitter_follows WHERE twitter_follows.user_1 = :username
);";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":username", $userID);
    $statement->execute();
      
    return $statement;
 }

/**
 * Show the list of the followers for the user logged in
 * This function will require two variables ($dbo)a database connection,
 * ($userID)a follower(session_["userlevel"])
 */
 function get_followers ($dbo,$userID){
    $query = "select twitter_users.username, twitter_users.ID, twitter_users.email 
from twitter_follows
inner join twitter_users 
on twitter_users.ID = twitter_follows.user_1
where user_2=:username
and twitter_follows.user_2 NOT IN (
  select twitter_users.ID FROM twitter_follows WHERE twitter_follows.user_2 = :username
);";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":username", $userID);
    $statement->execute();
      
    return $statement;
 }