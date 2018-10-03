<?php
session_start();
include('functions_stuff.php');
header("Location: http://einstein.etsu.edu/~raymondbr/Proj21/twitterHome.php?tweet=1");

if($_SERVER["REQUEST_METHOD"] != "POST"){
  exit();
} else {
      if (isset($_GET['tweet']))
      {
        header("Location: http://einstein.etsu.edu/~raymondbr/Proj21/twitterHome.php");
        exit(); 
      }
      
      
      $newTweet = clean_data($_POST['newTweet']);
      $query = "INSERT INTO twitter_tweets(tweet,twitter_user_id) values(:tweet,:userid)";
      $statement = $dbo->prepare($query);
      $statement->bindParam(":userid", $_SESSION['userlevel']);
      $statement->bindParam(":tweet", $newTweet);
      $statement->execute();
      close_db();
      exit();

    }
?>