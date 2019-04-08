<?php
    if ($_GET["admin_divert"]=="create"&&!isset($user))
    {
        display_user_form(null,null,$success);
    }
    else
    {
        if (is_array($user))
        {
            display_user_form($user,$errors,$success);
        }
        else
        {
            echo    "<div class='l_heading'>";
            echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>List Users</span>";
            echo    "</div>";
            $uc=0;
            while ($user=mysql_fetch_array($users))
            {
                if ($user["userID"]!=1)
                {
                    echo user_row($user);
                    if ($uc%4<3) echo item_spacer();
                    $uc++;
                }
            }
        }
    }
?>