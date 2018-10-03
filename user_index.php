<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 4/19/17
 * Time: 6:01 PM
 */

?>
<div style="padding-top: 8px; width: 100%; height: 50px; background-color: #FBFBFB; box-shadow: 0 2px 5px #E7E7E7;">
    <ul class="list-inline" style="float: right; margin: 0 auto;">
        <li>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                <input type="submit" class="btn btn-default" name="allUsers" value="All Users">
                <input type="submit" class="btn btn-default" name="following" value="Following">
                <input type="submit" class="btn btn-default" name="myFollowers" value="My Followers">
            </form>
        </li>
    </ul>
</div>
