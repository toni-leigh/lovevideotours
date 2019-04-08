<?php
    if ($_SESSION["user"])
    {
        
    }
    else
    {
        echo engage_form($user_check,"login",1);
        echo "<a href='/new-password'>Forgot Password?</a>";
    }
?>