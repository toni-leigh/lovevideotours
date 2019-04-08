<?php
    echo dump_include(array("level"=>"Cn-2","include_name"=>"item_controller.php"));
    $category_from_URL=parse_url_category($_GET["element_reference"]."/".$_GET["category1"]."/".$_GET["category2"]."/".$_GET["category3"]."/".$_GET["filter"]);
    //get the item details or the details of all items in this category
    if (is_category($category_from_URL,$item_type["itemTypeID"])||$category_from_URL=="")
    {
        $category=get_category_by_append($category_from_URL);
        include "control/get_".$item_type["itemType"]."s_controller.php";
    }
    else
    {
        //reset category from URL to drop the item ID in filter
        $item=get_item(array("i_type"=>$item_type,"i_ID"=>$_GET["filter"]));
        $_SESSION["item"]=$item; // store for ajax and functions
        if ($item["itemType"]=="videoitem")
        {
            //no item type in URL so use element ref as category
            $category_from_URL=parse_url_category($_GET["element_reference"]."/".$_GET["category1"]."/".$_GET["category2"]."/".$_GET["category3"]);
        }
        else
        {
            //item type dropped for other items
            $category_from_URL=parse_url_category($_GET["category1"]."/".$_GET["category2"]."/".$_GET["category3"]);
        }
        //single item display page
        $creator=get_user($item["userID"]);
        //entity details
        $entity_ID=$item["itemID"];
        $entity_type="item";
        $item_display=calculate_display($item,"item");
        if ($item_display)
        {
            $comments=get_comments($entity_ID,$entity_type);
        }
        //get map items - just the attractions and only if videoitem
        if ($item_type["itemTypeID"]==$vid_type_ID) $map_items=get_attractions();
    }
    
    if ($item_type["itemTypeID"]==3)
    {
        // populate the map, only on video item pages
        $_SESSION["sparams"]["map"]=1;
        $map_items=get_video_items($_SESSION["sparams"]);
    }
    
    //get category info
    $current_category=get_category_by_append($category_from_URL);
?>