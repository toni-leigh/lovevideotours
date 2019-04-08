<?php
    //get items
    if ($_GET["admin_divert"]=="edit"&&!isset($_GET["item_ID"]))
    {
        $item_type=get_item_type(2);
        $items=get_items($item_type,"","itemCreated desc");
    }
    elseif ($_GET["admin_divert"]=="edit"&&isset($_GET["item_ID"]))
    {
        $item=get_item($_GET["item_ID"],2);
    }
    //save the specific item details here (Blog table)
    //item will be reset by the outcome of $_POST processing
    if (isset($_POST["itemID"]))
    {
        //error check - first generic
        include "control/admin_item_error_check.php";
        //then specific to blog
        
        //save if successful, else set $item to post and reload form with errors
        if ($success)
        {
            if ($_POST["blogPropertyID"]=="any")
                $blog_property_ID=0;
            else
                $blog_property_ID=$_POST["blogPropertyID"];
            //save the basic item details and get item ID
            $item_ID=save_item($_POST,$item_URL_append,$item_type["itemTypeID"]);
            $dev=0;
            //then save the video item details
            if (is_numeric($_POST["itemID"]))
            {
                //update SQL
                $update_string="update Blog set blogPropertyID=".$blog_property_ID." ";
                $update_string=$update_string."where itemID=".$item_ID;
                site_query($update_string,"update Blog in admin-blog_controller",$dev);
            }
            else
            {
                //insert SQL
                $insert_string="insert into Blog (itemID,blogPropertyID";
                $insert_string=$insert_string.")";
                $insert_string=$insert_string." values ";
                $insert_string=$insert_string."(".$item_ID.",".$blog_property_ID;
                $insert_string=$insert_string.")";
                site_query($insert_string,"insert Blog in admin-blog_controller",$dev);
                //create the video item record too
            }
            //finally initialise the item for showing on page refresh
            $item=get_item($item_ID,$item_type["itemTypeID"]);
        }
        else
        {
            $item=$_POST;
        }
    }
?>