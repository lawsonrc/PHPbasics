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
header("Cache-Control: no-cache, must-revalidate");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bravo Home</title>
    <link rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
<?php include_once('header.php'); ?>
  <div class="container">
      
  <!-- tweetbox -->
    <div id="tweetModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Share a tweet</h4>
            </div>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <div class="modal-body">
                        <label for="tweet"></label>
                        <input class="textarea" style="height: 80px; width:570px; font-size: 150%; vertical-align: top;" type="text" id="tweet" name="newTweet">
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" value="Post" name="post">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
 <div class="container" style="width: 75%; height: 20%">
    <p style="margin: 0 auto; text-align: center;"><a data-target="#tweetModal" role="button" data-toggle="modal"><button class="btn btn-success">Say something!</button></a></p>
</div>
    <?php
      $dbo = connect_db();
  //add tweet to timeline
    if (isset($_POST['newTweet']) && $_POST['post']="Post") {
      $newTweet = clean_data($_POST['newTweet']);
      $query = "INSERT INTO twitter_tweets(tweet,twitter_user_id) values(:tweet,:userid)";
      $statement = $dbo->prepare($query);
      $statement->bindParam(":userid", $_SESSION['userlevel']);
      $statement->bindParam(":tweet", $newTweet);
      $statement->execute();
     
    }
    /*stop resubmissions of form
   
    <?php
   }*/
  
    //get tweets 
    
    $query = "SELECT * FROM twitter_tweets WHERE twitter_user_id IN (SELECT user_2 FROM twitter_follows WHERE user_1 = :id) ORDER BY ID DESC;";
    $statement = $dbo->prepare($query);
    $statement->bindParam(":id", $_SESSION['userlevel']);
    $statement->execute();
  ?>
    <div class="container" style="width: 75%; background: lightgray; margin: 0 auto; text-align: center;">

    <?php
    displayTweets($dbo,$statement);
    close_db();
    ?>

  </div>
  <?php include_once('footer.php'); ?>
</body>
</html>
 
<!-- Load JS -->
<script type='text/javascript' src='./bootstrap-3.3.7-dist/js/bootstrap.min.js'></script>