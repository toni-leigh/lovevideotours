<?php
    echo "</div>"; //close holder
    //load global js script
    echo "<script type='text/javascript' src='/form/global.js'></script>";
    //load in the javascript specific to this page
    if ($page["javascriptInclude"])
        echo "<script type='text/javascript' src='/form/".$page["URL"].".js'></script>";
    if ($item_type["itemType"])
        echo "<script type='text/javascript' src='/form/".$item_type["itemType"].".js'></script>";
    echo    "</body>";
?>