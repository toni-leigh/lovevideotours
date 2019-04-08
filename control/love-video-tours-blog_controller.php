<?php
    if (is_array($item))
    {
        $item_images=get_images("item",$item["itemID"]);
    }
    if (isset($lvt_blog_posts))
        $_SESSION["vitem_set"]=$lvt_blog_posts;
    else
        $lvt_blog_posts=get_items(array("i_type"=>$item_type,"order_by"=>"itemCreated desc"));
    if (mysql_num_rows($lvt_blog_posts)>0)
    {
        // load the set of video items into an array in the $_SESSION for traversal from item and full screen pages
        unset($_SESSION["vitem_set"]);
        $c=0;
        while ($vitem=mysql_fetch_array($lvt_blog_posts))
        {
            $_SESSION["vitem_set"][$c]["item"]=$vitem;
            $c++;
        }    
        // reset, return
        mysql_data_seek($lvt_blog_posts,0);
    }
?>