<?php
    if (is_array($edit_page))
    {
        echo "<div id='admin_form'>";
        echo "<form method='post' action=''>";
        echo    "<input type='hidden' name='page_ID' value='".$edit_page["pageID"]."'/>";
        text_field("pageHTML","Page HTML",$edit_page["pageHTML"],$errors,"textarea"); 
        echo    "<input type='submit' name='submit' value='submit'/>";
        echo "</form>";
        echo "</div>";
    }
    else
    {
        while ($master_page=mysql_fetch_array($master_pages))
        {
            get_pages($master_page["pageID"],1,0,"edit");
        }
    }
?>