<?php
    if (authorised($page))
        if (isset($_GET["admin_divert"]))
            include $device."form/admin-".$item_type["itemType"].".php";
        else
            if ($page["justUserHTML"])
                echo $page["pageHTML"];
            else
                include $device."form/".$page["URL"].".php";
    else
        echo "You are not authorised to view this page";
?>
