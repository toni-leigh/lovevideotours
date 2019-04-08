<?php
    /*
     Gets all the categries with parentID=0, the top level categories of any given hierarchy
    */
    function get_master_categories($item_type_ID)
    {
        $get_category_query=site_query("select * from Category where parentID=0 and itemTypeID=".$item_type_ID." order by displayOrder","get_master_categories()",$dev);
        return $get_category_query;
    }
    /*
     Gets the immediate sub categories of a given parent
    */
    function get_sub_categories($category_ID)
    {
        $get_sub_category_query=site_query("select * from Category where parentID=".$category_ID." order by displayOrder","get_sub_categories()",$dev);
        return $get_sub_category_query;
    }
    function get_category($category_ID)
    {
        $dev=0;
        $category_string="select * from Category where categoryID=".$category_ID;
        $category_query=site_query($category_string,"get_category()",$dev);
        return mysql_fetch_array($category_query);
    }
    function get_category_by_append($url_append)
    {
        $get_category_query=site_query("select * from Category where categoryUrlAppend='".$url_append."'","get_category_by_append()",$dev);
        return mysql_fetch_array($get_category_query);
    }
    /*
     Recursive function that gets a set of categories ordered by their hierarchy
     $root - is the ID of the current category to output (not the same as $current) - when the function is called for the first time
         this is the master category of hierarchy - in most cases the master categories (parentID=0)are looped through wherever the
         hierarchy is displayed
     $level - is the depth level for the current category output
     $current - is item currently selected by the user of the site - the item to be highlighted in a menu for example - NOT THE SAME
         AS THE CURRENT RECURSIVE ITERATION!
     $type_reference - tells the function which Category table to query
     $reference - points the function at a piece of code that generates the output of this Category - defaults to 'menu'
        'menu' - the category is being output as a menu link
        'drop_down' - the category is being output as a drop-down menu for selection
        'admin' - the category is being output as a list item for Love Your Larders admin
    */
    function get_categories($root,$level,$current,$reference="menu")
    {
        //we need a numeric value for category - if category is not present becasue we have just landed then set to 0 so as not to
        //break the open menu item selection below
        if (!is_numeric($current)) $current=0;
        $table_reference=ucfirst($type_reference)."Category";
        $key_reference=$type_reference."CategoryID";
        $category=get_category($root);
        $category_item_count=1;
        if ($reference=="select")
        {
            $cs.="<option value='".$category["categoryID"]."'";
            if ($current==$category["categoryID"])
                $cs.=" selected='selected' ";
            $cs.=">".$category["categoryName"]."</option>";
        }
        else
        {
            if ($current==$category["categoryID"]) $id=" id='snav_sel' ";
            $cs.="<span ".$id." class='snav_item snav".$level." left'><a href='".build_category_link($category)."'>".$category["categoryName"]."</a></span>";
        }
        $count_subs=get_sub_categories($root);
        if (mysql_num_rows($count_subs)>0)
        {
            if ($reference=="menu")
            {
                //for menus an extra bit of logic to keep the item open - using the parent will keep both the categories open if this is
                //a third level current category
                $current_selected_category=get_category($current);
                if (is_numeric($current_selected_category["parentID"]))
                {
                    $current_selected_category_parent=get_category($current_selected_category["parentID"]);                    
                }
                if ($current==$category["categoryID"]||$current_selected_category["parentID"]==$category["categoryID"]||$current_selected_category_parent["parentID"]==$category["categoryID"])
                {
                    $level++;
                    while ($sub=mysql_fetch_array($count_subs))
                    {
                        $cs.=get_categories($sub["categoryID"],$level,$current);
                    }
                }
            }
            else
            {
                //for everything else the whole shebang should be displayed - so no logic like above
                $level++;
                while ($sub=mysql_fetch_array($count_subs))
                {
                    $cs.=get_categories($sub["categoryID"],$level,$current,$reference);
                }
            }
        }
        return $cs;
    }
?>