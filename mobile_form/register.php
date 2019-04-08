<?php
    if ($_SESSION["user"])
    {
        
    }
    else
    {
        echo engage_form($user_check,"register",1);
    }
?>