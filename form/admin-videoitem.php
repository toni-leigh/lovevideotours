<?php
    if ($_GET["admin_divert"]=="create")
    {
        video_item_form($item,$item_type,$errors,$success);
    }
    else
    {
        if (is_array($item))
        {
            if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
            {
                echo "This is not your item to edit";
            }
            else
            {
                video_item_form($item,$item_type,$errors,$success);
            }
        }
        else
        {
            echo    "<div class='l_heading'>";
            echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>List VideoItems</span>";
            echo    "</div>";
            //list the items here for editing
            $ac=0;
            $pr_fl="";
            while ($item=mysql_fetch_array($items))
            {
                $curr_fl=strtoupper(substr($item["itemName"],0,1));
                if ($pr_fl!=$curr_fl)
                {
                    $ac=0;
                    echo "<div class='ad_ilist_heading'>".$curr_fl."</div>";
                }
                echo ad_item_panel($item);
                if ($ac%4<3) echo item_spacer();
                $ac++;
                $pr_fl=$curr_fl;
            }
        }
    }
?>