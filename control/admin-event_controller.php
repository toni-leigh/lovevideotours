<?php
    //get items
    if ($_GET["admin_divert"]=="edit"&&!isset($_GET["item_ID"]))
    {
        $items=get_items($item_type,"","itemCreated desc");
    }
    elseif ($_GET["admin_divert"]=="edit"&&isset($_GET["item_ID"]))
    {
        $item=get_item($_GET["item_ID"],6);
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
            //save the basic item details and get item ID
            $_POST["userID"]=1;
            $item_ID=save_item($_POST,$item_URL_append,$item_type["itemTypeID"]);
            $dev=0;
            $from_date=reverse_date($_POST["fromDate"]);   
            $to_date=reverse_date($_POST["toDate"]);       
            //then save the video item details
            if (is_numeric($_POST["itemID"]))
            {
                //update SQL
                $update_string="update Event set toDate='".$to_date."', ";
                $update_string.="fromDate='".$from_date."', ";
                $update_string.="nearestTown='".$_POST["nearestTown"]."', ";
                $update_string.="county='".$_POST["county"]."', ";
                $update_string.="longitude=".$_POST["longitude"].", ";
                $update_string.="latitude=".$_POST["latitude"]." ";
                $update_string=$update_string."where itemID=".$item_ID;
                site_query($update_string,"update Blog in admin-blog_controller",$dev);
                //save item specific details here
            }
            else
            {
                //insert SQL
                $insert_string="insert into Event (itemID,toDate,fromDate,nearestTown,county,longitude,latitude";
                $insert_string=$insert_string.")";
                $insert_string=$insert_string." values ";
                $insert_string=$insert_string."(".$item_ID.",'".$to_date."','".$from_date."','".$_POST["nearestTown"]."','".$_POST["county"]."',".$_POST["longitude"].",".$_POST["latitude"];
                $insert_string=$insert_string.")";
                site_query($insert_string,"insert Event in admin-event_controller",$dev);
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