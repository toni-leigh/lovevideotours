<?php
    echo dump_include(array("level"=>2,"include_name"=>"item.php"));
    if (isset($master_categories))
        while ($master_category=mysql_fetch_array($master_categories))
            get_categories($master_category["categoryID"],1,$current_category["categoryID"]);
    if (is_array($item))
    {
        if ($item_display)
        {
            //recommend buttons
            if ($_SESSION["user"])
                echo build_social_action_button(array("e_ID"=>$item["itemID"],"e_type"=>"item","e_stype"=>$item["itemType"],"a_type"=>"recommend"));
            include $device."form/".$item_type["itemType"].".php"; 
        }
        else
            echo "This item cannot be found";
    }
    else
    {
        if ($_SESSION["user"])
            if (is_numeric($current_category["categoryID"]))
                echo build_social_action_button(array("e_ID"=>$current_category["categoryID"],"e_type"=>"category","e_stype"=>$current_category["itemType"],"a_type"=>"follow"));
        include $device."form/".$item_type["itemType"].".php"; 
    }
?>