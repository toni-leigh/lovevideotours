<?php
    if (is_array($item)||$_GET["admin_divert"]=="create")
    {
        if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
        {
            echo "This is not your item to edit";
        }
        else
        {
            echo "<div id='centre_admin_form'>";
            echo "<p>Updates will appear on your property page - in their own tab. You can keep people up to date with what is happening in the area and with the property.</p>";
            echo "<p>If you have more than one property then select the property from the list (which is hidden if you just have one) and it will appear on that property. Alternatively select 'general' to have the update appear on all properties.</p>";
            if (isset($_GET["item_ID"]))
                $about=get_item($_GET["item_ID"],3);
            build_blog_form($item,$item_type,$errors,$about);
            echo "</div>";
        }
    }
    else
    {
        //list the items here for editing
        $counter=0;
        $item_count=mysql_num_rows($items);
        while ($item=mysql_fetch_array($items))
        {
            $item_images=get_images("item",$item["itemID"]);
            $item_image=mysql_fetch_array($item_images);
            echo "<div class='admin_blog_panel'>";           
            echo    "<span class='admin_blog_panel_text'>";
            echo        "<a href='/control-room/blog/edit/".$item["itemID"]."'>'".$item["itemName"]."'";
            if ($item["blogPropertyID"]==0)
                echo " a general post - applies to all property";
            else
            {
                $property=get_item($item["blogPropertyID"],3);
                echo " about ".$property["itemName"];
            }
            echo        "</a>";
            echo    "</span>";
            echo    "<span class='admin_blog_text'>".$item["itemHTML"]."</span>";
            echo    "<span class='edit_link'>";
            echo        "<a href='/control-room/blog/edit/".$item["itemID"]."'>EDIT"; 
            echo        "</a>";
            echo    "</span>";
            echo "</div>";
            $counter++;
        }
    }
?>