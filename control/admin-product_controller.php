<?php
    echo dump_include(array("level"=>"Cn2","include_name"=>"admin-product_controller.php"));
    if (is_numeric($_POST["itemID"]))
    {
        //update SQL
        /*$update_string="update Product ";
        $update_string=$update_string."where itemID=".$item_ID;*/
        /*$update_string="update ProductVariation set price=".$_POST["price"]." ";
        $update_string=$update_string."where itemID=".$item_ID;
        
        site_query($update_string,"update Product in admin-product_controller",$dev);*/
        //save item specific details here
    }
    else
    {
        //insert SQL
        $insert_string="insert into Product (itemID";
        $insert_string=$insert_string.")";
        $insert_string=$insert_string." values ";
        $insert_string=$insert_string."(".$item_ID;
        $insert_string=$insert_string.")";
        site_query($insert_string,"insert Product in admin_videoitem_controller",$dev);         
        //create the video item record too
    }
?>