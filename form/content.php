<?php
    echo "<section id='content'>";
    echo dump_include(array("level"=>1,"include_name"=>"content.php [content]"));
    if ((isset($_GET["admin_divert"])||$_GET["element_reference"]=="image-upload"||$_GET["element_reference"]=="sales-list")&&is_array($_SESSION["user"]))
        echo adnav_bar();
    else
        if (!is_array($item)&&$page["pageID"]!=18) // dont do this for search results
            echo h1(array("page"=>$page,"user"=>$user,"item"=>$item,"category"=>$current_category));
    if (authorised($page))
        if (isset($_GET["admin_divert"]))
            if ($page["pageID"]!=40)
                include $device."form/admin-".$item_type["itemType"].".php";
            else
                include $device."form/manage-users.php";                
        else
            if ($page["justUserHTML"])
                echo $page["pageHTML"];
            else
                include $device."form/".$page["URL"].".php";
    else
        echo entity_not_found();
    echo "</section>";
?>