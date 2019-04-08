<?php
    if ($password_sent&&isset($_POST["password_email"]))
        echo "New password sent to ".$_POST["password_email"];
    echo "<form method='post' action=''>";
    echo    "<input type='text' name='password_email'/>";
    echo    "<input id='password_submit' class='submit_button button' type='submit' name='submit' value='Get New Password'/>";
    echo "</form>";
?>