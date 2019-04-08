<?php
    //engage buttons
    if ($_SESSION["user"])
    {
        echo "<span id='header_logout'>";
        echo    "<form method='post' action=''/>";
        echo        "<input type='hidden' name='logout' value='logout'/>";
        echo        "<input id='logout_submit' class='submit_button button' type='submit' name='submit' value='logout'/>";
        echo    "</form>";
        echo "</span>";
    }
    else
    {
        echo "<span id='header_login'><a href='/login'>login</a></span>";
        echo "<span id='header_register'><a href='/register'>register</a></span>";
        echo "<span id='header_facebook'></span>";
    }
    if (is_array($user_check)||!isset($user_check)||$page["URL"]=="register"||$page["URL"]=="login")
        $engage_state="none";
    else
        $engage_state="block";
    $dev=0;
    dev_dump($user_check,"engage.php - user check message",$dev);
    echo "<span style='display:".$engage_state.";' id='engage_form_display'>";
    echo dump_include(array("level"=>2,"include_name"=>"engage.php"));
    if (!is_array($_SESSION["user"]))
        echo engage_form($user_check,"login");
    echo "</span>";
    echo open_script();
    echo "if (document.getElementById('header_login'))";
    echo    "document.getElementById('header_login').innerHTML='<span onclick=\"engage_form(\'login\')\">login</span>';";
    echo "if (document.getElementById('header_register'))";
    echo    "document.getElementById('header_register').innerHTML='<span onclick=\"engage_form(\'register\')\">register</span>';";
    echo close_script();
?>