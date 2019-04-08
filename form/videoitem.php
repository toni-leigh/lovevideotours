<?php
    if (is_array($item))
    {
        // a bar for traversing through items
        echo vi_traverse(array("item"=>$item));
        echo h1(array("page"=>$page,"user"=>$user,"item"=>$item,"category"=>$current_category));
        // displays the video / image panel
        echo "<div id='item_details_top'>";
        echo vi_image_vid(array("item"=>$item,"item_images"=>$item_images));
        echo vi_details(array("item"=>$item,"contact_success"=>$contact_success));
        echo "</div>";
        echo vi_map_heading();
        echo vi_filter_panel();
        echo vi_map(array("item"=>$item,"map_items"=>$map_items));
        echo vi_recommends($recommendations);
        /*echo "<div id='item_details_bottom'>";
        if ($item["parentID"]==5)
        {
            echo "<div id='view_accomodation_link'>";
            echo "<a href='/accommodation-search/".$item["itemUrlAppend"]."'>view nearby accommodation</a>";
            echo "</div>";
        }
        echo "</div>";*/
        include "form/videoitem_dynamic_js.php";
    }
    else
    {
        echo "<div id='ie_h1_fix'>";
        // filter and map at the top for attractions (no on map heading either)
        if ($category["categoryID"]!=3)
        {
            echo vi_filter_panel($category["categoryID"]);
            echo vi_map(array("map_items"=>$map_items));
        }
        echo "<div id='vitem_list'>";
        echo    item_list($items);
        echo "</div>";
        // or at bottom for property
        if ($category["categoryID"]==3)
        {
            echo vi_map_heading();
            echo vi_filter_panel($category["categoryID"]);
            echo vi_map(array("map_items"=>$map_items));
        }
        echo "</div>";
    }
?>