<?php
session_start();
include('functions_stuff.php');
header("Location: ./twitterHome.php");

/** if($_SERVER["REQUEST_METHOD"] != "POST"){
  exit();
} else { **/
  $dbo = connect_db();
    
if(likeCheck($dbo, $_SESSION['userlevel'], $_POST['tweetid']) == false){
      $id = $_POST['tweetid'];
      $user = $_SESSION['userlevel'];

      $query = "INSERT INTO twitter_likes(user_id,tweet_id) VALUES(:user,:id);";
      $statement2 = $dbo->prepare($query);
      $statement2->bindParam(":user",$user);
      $statement2->bindParam(":id",$id);
      $statement2->execute();
    }else{
      $id = $_POST['tweetid'];
      $user = $_SESSION['userlevel'];

      $query = "DELETE FROM twitter_likes WHERE user_id = :user AND tweet_id = :id;";
      $statement2 = $dbo->prepare($query);
      $statement2->bindParam(":user",$user);
      $statement2->bindParam(":id",$id);
      $statement2->execute();
    }
  close_db();
  exit();
//}
?>