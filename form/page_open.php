<?php
    if (show_map(array("page"=>$page,"item_type"=>$item_type)))
        echo "<body class='font_norm' onload='initialize()'>";
    else
        echo "<body class='font_norm'>";
    echo "<!--[if IE 7]>";
    echo "<div id='ie7_font'>";
    echo "<![endif]-->";
    echo "<!--[if IE 8]>";
    echo "<div id='ie8_font'>";
    echo "<![endif]-->";
    if (($item["itemTypeID"]==3||$item["itemTypeID"]==5)&&!(isset($_GET["admin_divert"])||$_GET["element_reference"]=="image-upload"))
    {
        // display the hidden large video and image viewer panel - right at the top of the page as this will cover whole page
        include "form/full_screen.php";
    }
    if (strlen($page["importEditorElements"]))
        import_editor($page["importEditorElements"]);
    echo "<div id='holder'>";
?>