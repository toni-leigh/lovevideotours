<?php
    //get items
    if ($_GET["admin_divert"]=="edit"&&!isset($_GET["item_ID"]))
    {
        $items=get_items($item_type,"","itemCreated desc");
    }
    elseif ($_GET["admin_divert"]=="edit"&&isset($_GET["item_ID"]))
    {
        $item=get_item($_GET["item_ID"],5);
    }
    //save the specific item details here (Blog table)
    //item will be reset by the outcome of $_POST processing
    if (isset($_POST["itemID"]))
    {
        // blog errors
        //save if successful, else set $item to post and reload form with errors
        if ($success)
        {
            //save the basic item details and get item ID
            $_POST["userID"]=1;
            $dev=0;
            //then save the video item details
            if (is_numeric($_POST["itemID"]))
            {
                //update SQL
                /*$update_string="update Blog ";
                $update_string=$update_string."where itemID=".$item_ID;*/
                //site_query($update_string,"update Blog in admin-blog_controller",$dev);
                //save item specific details here
            }
            else
            {
                //insert SQL
                $insert_string="insert into LVTBlog (itemID";
                $insert_string=$insert_string.")";
                $insert_string=$insert_string." values ";
                $insert_string=$insert_string."(".$item_ID;
                $insert_string=$insert_string.")";
                site_query($insert_string,"insert LVTBlog in admin-blog_controller",$dev);
                //create the video item record too
            }
            //finally initialise the item for showing on page refresh
            build_rss_xml();
        }
        else
        {
            $item=$_POST;
        }
    }
?>