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
            build_lvt_blog_form($item,$item_type,$errors,$success);
            echo "</div>";
        }
    }
    else
    {
        echo    "<div class='l_heading'>";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>List LVT Blogs</span>";
        echo    "</div>";
        //list the items here for editing
        $bc=0;
        while ($item=mysql_fetch_array($items))
        {
            if ($user["userID"]!=1)
            {
            echo lvt_blog_row($item);
                if ($bc%4<3) echo item_spacer();
                $bc++;
            }
        }
    }
?>