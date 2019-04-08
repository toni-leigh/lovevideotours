<?php
    /*
     defines whether a value is a category or not for this item type
    */
    function is_category($category_URL,$item_type_ID)
    {
        $category=site_query("select * from Category where categoryUrlAppend='".$category_URL."' and itemTypeID=".$item_type_ID,"is_category()");
        return mysql_num_rows($category);
    }
    /*
     returns the details of an individual item - this will return a hidden item, but if a hidden item is viewed by an unauthorised use the hidden content panel will be displayed. Also returns the associated supplier details
     $reference = the item ID, can be either numeric ID or URL provided append string
     $item_type = can be text item type or array
     $variation_ID = the specific variation to return, the default is main gets the main variation
    */
    function get_item($in)
    {
        $dev=0;
        if (!is_array($in["i_type"]))
            $item_type=get_item_type($in["i_type"]);
        else
            $item_type=$in["i_type"];
        //set the reference to point at the right Item column
        if (is_numeric($in["i_ID"]))
            $reference_clause="Item.itemID=".$in["i_ID"]." ";
        else
            $reference_clause="Item.itemUrlAppend='".$in["i_ID"]."' ";
            
        //initailise with common tables for all queries
        $item_string="select * from Item, Category, ".$item_type["itemTable"].", User, ItemType ";
        
        //common restriction clause - all item queries have to join their tables like this
        $restriction_clause="and Item.categoryID=Category.categoryID and Item.userID=User.userID and Item.itemID=".$item_type["itemTable"].".itemID and Item.itemTypeID=ItemType.itemTypeID ";
        
        // check for variations, we need to get item even if no variations
        if ($item_type["itemVariationTable"])
        {
            if (is_numeric($in["i_ID"]))
            {
                $item_ID=$in["i_ID"];
            }
            else
            {
                $item_ID_query=site_query("select * from Item where itemUrlAppend='".$in["i_ID"]."'","get numeric id");
                if (mysql_num_rows($item_ID_query)>0)
                {
                    $item_ID_array=mysql_fetch_array($item_ID_query);
                    $item_ID=$item_ID_array["itemID"];
                }
                else
                    $item_ID=0;
            }
            $variations=site_query("select * from ".$item_type["itemVariationTable"]." where itemID=".$item_ID,"variation check in get_item()",$dev);
            $vcount=mysql_num_rows($variations);
        }
        else
            $vcount=0;
        
        //if the item has a variatrion table we use that when retrieving the item
        if ($item_type["itemVariationTable"]&&$vcount)
        {
            //append the variation table
            $item_string=$item_string.", ".$item_type["itemVariationTable"]." ";
            //set the variation to look at
            if (is_numeric($in["v_ID"]))
                $variation_clause="and ".$item_type["itemTable"].".itemID=".$item_type["itemVariationTable"].".itemID and ".$item_type["itemVariationTable"].".variationID=".$in["v_ID"]." ";
            else
                $variation_clause="and ".$item_type["itemTable"].".itemID=".$item_type["itemVariationTable"].".itemID order by main desc limit 1 ";
            dev_dump($variation_clause,"get_item() - variation clause",$dev);
            
            //build item query with variationID
            $item_string=$item_string." where ".$reference_clause.$restriction_clause.$variation_clause;
            $item_query=site_query($item_string,"get_item() - variation included",$dev);
        }
        else
        {
            $item_string=$item_string." where ".$reference_clause.$restriction_clause;
            $item_query=site_query($item_string,"get_item() - not a variation item",$dev);              
        }
        dev_dump($item_query,"get_item() - final query",$dev);
        $item=mysql_fetch_array($item_query);
        dev_dump($item,"get_item() - actual item",$dev);
        return $item;
    }
    /*
     gets product mysql array set
     $extra_where = extra where clauses, such as used by out of stock
     $order_by = how to order the results
     $limit = sets a value to limit the result to
     $show_out_of_stock = shows all products even ones out of stock, we show out of stock products on the front end category lists but not in the recommendation panels for example
     $show_hidden = will show the hidden products (only set for admin calls)
    */
    function get_items($in)
    {
        $dev=0;
        if (!is_array($in["i_type"]))
            $item_type=get_item_type($in["i_type"]);
        else
            $item_type=$in["i_type"];
        //start statement inc. generic where - connects Product to it's Supplier and to it's variation
        $items_string="select * from Item, Category, ".$item_type["itemTable"].", User ";
        if ($item_type["itemVariationTable"]&&$item_type["itemVariationMain"]&&!$in["ignore_variations"])
        {
            $items_string=$items_string.", ".$item_type["itemVariationTable"]." ";
            $variation_clause="and Item.itemID=".$item_type["itemVariationTable"].".itemID and ".$item_type["itemVariationTable"].".main=1 ";
        }
        else
            $variation_clause="";
        //add user and category data
        $items_string=$items_string."where Item.categoryID=Category.categoryID and Item.userID=User.userID and Item.itemID=".$item_type["itemTable"].".itemID ".$variation_clause." ";
        //performs show hidden logic
        if (is_numeric($in["only"]))
            $items_string=$items_string."and Item.itemDisplay=1 and User.userDisplay=1 ";
        else
            $items_string=$items_string."";
        //set to restrict whether stock is shown
        if (is_numeric($in["only"])&&$item_type["inStockItem"])
            $items_string=$items_string."and Item.itemID in (select itemID from ".$item_type["itemVariationTable"]." where inStock=1) ";
        //add extra where
        if (strlen($in["extra_where"])>0)
            $extra_where=" and ".$in["extra_where"]." ";
        $items_string=$items_string.$extra_where;
        //orderby and limit
        if (strlen($in["order_by"])>0)
            $items_string=$items_string."order by ".$in["order_by"]." ";
        if (is_numeric($in["limit"]))
            $items_string=$items_string."limit ".$in["limit"];
        
        dev_dump($items_string,"get_items()",$dev);
        $items_query=site_query($items_string,"get_items()",$dev);        
        return $items_query;
    }
    /*
     gets item variations
    */
    function get_item_variations($item,$in_stock_only=0,$order_by="main",$only=1)
    {
        $dev=0;
        $item_type=get_item_type($item["itemTypeID"]);
        $extra_where="";
        if ($only)
            $extra_where=$extra_where." and ".$item_type["variationColumn"]."Display=1 ";
        if ($in_stock_only)
            $extra_where=$extra_where." and inStock=1 ";
        $item_variation_string="select * from ".$item_type["itemVariationTable"]." where itemID=".$item["itemID"].$extra_where." order by ".$order_by;
        $item_variation_query=site_query($item_variation_string,"get_item_variations",$dev);
        return $item_variation_query;
    }
    function save_item($item,$item_URL_append,$item_type_ID)
    {
        if (is_numeric($_POST["itemID"]))
        {
            //update SQL
            $update_string="update Item set itemName='".addslashes($item["itemName"])."', ";
            $update_string=$update_string."itemTweet='".addslashes($item["itemTweet"])."', ";
            $update_string=$update_string."itemTags='".addslashes($item["itemTags"])."', ";
            $update_string=$update_string."itemHTML='".addslashes($item["itemHTML"])."',";
            $update_string=$update_string."categoryID=".$item["categoryID"].",";
            $update_string=$update_string."itemUrlAppend='".$item_URL_append."' ";
            $update_string=$update_string."where itemID=".$item["itemID"];
            site_query($update_string,"update save_item()",$dev);
            $item_ID=$item["itemID"];
            //save item specific details here
        }
        else
        {
            //insert SQL
            $insert_string="insert into Item (";
            $insert_string=$insert_string."itemName,";
            $insert_string=$insert_string."itemTweet,";
            $insert_string=$insert_string."itemTags,";
            $insert_string=$insert_string."itemHTML,";
            $insert_string=$insert_string."userID,";
            $insert_string=$insert_string."categoryID,";
            $insert_string=$insert_string."itemUrlAppend,";
            $insert_string=$insert_string."itemTypeID,";
            $insert_string=$insert_string."itemDisplay";
            $insert_string=$insert_string.")";
            $insert_string=$insert_string." values ";
            $insert_string=$insert_string."(";
            $insert_string=$insert_string."'".addslashes($item["itemName"])."',";
            $insert_string=$insert_string."'".addslashes($item["itemTweet"])."',";
            $insert_string=$insert_string."'".addslashes($item["itemTags"])."',";
            $insert_string=$insert_string."'".addslashes($item["itemHTML"])."',";
            $insert_string=$insert_string.$_SESSION["user"]["userID"].",";
            $insert_string=$insert_string.$item["categoryID"].",";
            $insert_string=$insert_string."'".$item_URL_append."',";
            $insert_string=$insert_string.$item_type_ID.",";
            $insert_string=$insert_string.$_SESSION["user"]["userDisplay"];
            $insert_string=$insert_string.")";
            site_query($insert_string,"insert save_item()",$dev);
            $item_ID=mysql_insert_ID();
            //create the video item record too
        }
        return $item_ID;
    }
    /*
     DISPLAY FUNCTIONS THAT APPLY TO ALL ITEMS
    */
    /*
     displays an item panel
    */
    function item_list($items,$width=4)
    {
        $counter=0;
        $gc=1;
        $item_count=mysql_num_rows($items);
        $il="";
        while ($item=mysql_fetch_array($items))
        {
            if ($counter%($width*4)==0&&$item_count>16)
            {
                $il.="<span class='i_group_counter'>";
                if ($gc*16>$item_count)
                    $il.=((($gc-1)*16)+1)." - ".$item_count;
                else
                    $il.=((($gc-1)*16)+1)." - ".($gc*16);
                if ($gc>1)
                    $il.="<a href='#'><span class='left orange'>back to top</span><span class='circle_icon margin_left' style='background-position: -266px -203px;'></span></a>";
                $il.="</span>";
            }
            if ($counter%$width==0) $il.="<span class='i_row".$width."'>";
            $il.=item_panel($item);
            if ($counter%$width<$width-1) $il.=item_spacer();
            if ($counter%$width==$width-1||$counter+1==$item_count) $il.="</span>";
            $counter++;
            if ($counter%($width*4)==0&&$item_count>16)
            {
                $gc++;
                $il.="<span class='i_group_div'></span>";
            }
        }
        return $il;
    }
    function item_panel($item)
    {
        $item_images=get_images("item",$item["itemID"]);
        $item_image=mysql_fetch_array($item_images);
        $ip="<span class='i_panel left'>";
        if ($item_image["largeSquarePath"]=="") $ipath="img/missing220.png"; else $ipath=$item_image["largeSquarePath"];
        $ip.="<a href='".build_item_link($item)."'><img src='/".$ipath."' alt='Thumbnail image of ".$item["itemName"].", links to details page' width='198' height='198'/></a>";
        if (strlen($item["videoSRC"])>0)
            $ip.="<span class='i_panel_icon_vid'>";
        else
            $ip.="<span class='i_panel_icon'>";
        if (strlen($item["videoSRC"]))
            $ip.=vi_vid_icon();
        $ip.=vi_cat_icon($item);
        $ip.="</span>";
        if (strlen($item["itemName"])>19) $iname=substr($item["itemName"],0,17)." .."; else $iname=$item["itemName"];
        if (strlen($item["itemTweet"])>70) $itweet=substr($item["itemTweet"],0,70)." .."; else $itweet=$item["itemTweet"];
        $ip.="<a href='".build_item_link($item)."'><span class='i_panel_name'>".stripslashes($iname)."</span></a>";
        $ip.="<span class='i_panel_tweet'>".stripslashes($itweet)."</span>";
        $ip.="</span>";
        return $ip;
    }
    function item_spacer()
    {
        return "<span class='i_spacer'></span>";
    }
    function item_images($item,$height)
    {
        echo    "<span id='item_details_images'>";
        $item_images=get_images("item",$item["itemID"]);
        $image_count=mysql_num_rows($item_images);
        $item_image=mysql_fetch_array($item_images);
        echo       "<div id='main_videoitem_image'><img src='/".$item_image["largeSquarePath"]."' alt='Image of ".$item["itemName"]."' width='296' height='296' onclick='show_large_panel(\"".$item_image["largeScalePath"]."\",\"".$height."\",\"".str_replace("'","",$item_image["imageName"])."\",\"".str_replace("'","",$item["itemName"])."\")'/></div>";
        echo       "<span id='item_thumbnails'>";
        $counter=0;
        $item_images=get_images("item",$item["itemID"]);
        $image_count=mysql_num_rows($item_images);
        while ($item_image=mysql_fetch_array($item_images))
        {
            if ($counter%3==0) echo "<span class='item_thumbnail_image_row'>";
            echo "<img src='/".$item_image["mediumSquarePath"]."' alt='Thumbnail of ".$item["itemName"]."' width='86' height='86' onmouseover='set_videoitem_image(\"".$item_image["largeSquarePath"]."\",\"".$item_image["largeScalePath"]."\",\"".$height."\",\"".str_replace("'","",$item_image["imageName"])."\",\"".str_replace("'","",$item["itemName"])."\")' onclick='show_large_panel(\"".$item_image["largeScalePath"]."\",\"".$height."\",\"".str_replace("'","",$item_image["imageName"])."\",\"".str_replace("'","",$item["itemName"])."\")'/>";
            if ($counter%3<2) echo "<span class='item_thumbnail_image_row_spacer'></span>";
            if ($counter%3==2||$counter+1==$image_count) echo "</span>";
            $counter=$counter+1;
        }
        echo       "</span>";
        echo    "</span>";
    }
?>