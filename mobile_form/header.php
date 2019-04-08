<?php
    echo "<div id='fb-root'></div>";
    echo    "<script src='http://connect.facebook.net/en_US/all.js'></script>";
    echo    "<script>";
    echo        "FB.init({";
    echo        "appId:'".$facebook_appid."', cookie:true,";
    echo        "status:true, xfbml:true"; 
    echo        "});";
    echo    "</script>";
    echo "<header>";
    echo dump_include(array("level"=>1,"include_name"=>"header.php"));
    echo    "<a href='/'>";
    echo        "<div id='logo'>";
    echo        "</div>";
    echo    "</a>";
    include $device."form/engage.php";
    echo    "<span id='basket_panel'>";
    echo       build_html_basket();
    echo    "</span>";
    include $device."form/search.php";
    echo "</header>";
?>