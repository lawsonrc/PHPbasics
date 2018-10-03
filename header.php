<!-- Header -->
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container"></div>
        <div class="navbar-header">
            <a class="navbar-brand" href="http://einstein.etsu.edu/~millerwt/Twitter/twitterHome.php">Bravo</a>
            
        </div>
        <div id="navbar" class="navbar-collapse collapse">
           
            <?php
            if (isset($_SESSION['loggedin'])) {
            ?>
            <div class="nav navbar-nav navbar-right dropdown" style="position: relative; padding-top: 15px; padding-right: 10%;">
                <a class="navbar-right dropdown-toggle" role="button" data-toggle="dropdown"><?php echo $_SESSION['username']; ?><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="./user_x.php/?username=<?php echo $_SESSION['username']; ?>" role="button"><strong><?php echo $_SESSION['name']; ?></strong><br>View profile</a></li>
                    <li class="divider"></li>
		                <li><a href="./twitter.php" role="button">Find Users</a></li>
		                <li class="divider"></li>
                    <li><a type="submit" href="twitterLogout.php" role="button">Logout</a>
                    </li>
                </ul>
            </div>
            <?php

            }
            ?>
        </div>
    </div>
</div>
<br><br><br>