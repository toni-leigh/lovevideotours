<?php
    echo "</div>"; //close holder
    //load global js script
    echo "<script type='text/javascript' src='/form/global.js'></script>";
    //load in the javascript specific to this page
    if ($page["javascriptInclude"])
        echo "<script type='text/javascript' src='/form/".$page["URL"].".js'></script>";
    if ($item_type["itemType"]=="videoitem")
        echo "<script type='text/javascript' src='/form/videoitem.js'></script>";
    if ($_GET["admin_divert"]=="variations")
        echo "<script type='text/javascript' src='/form/variations.js'></script>";  
    echo "<!--[if IE]>";
    echo "</div>"; // close ie_font
    echo "<![endif]-->";      
    echo    "</body>";
?>