<?php
    if (isset($_POST["page_ID"]))
    {
        site_query("update Page set pageHTML='".$_POST["pageHTML"]."' where pageID=".$_POST["page_ID"],"update Page in page-edit_controller.php");
        $edit_page=get_page($_POST["page_ID"]);
    }
    if (is_numeric($_GET["page_ID"]))
    {
        $edit_page=get_page($_GET["page_ID"]);
    }
    else
    {
        $master_pages=site_query("select * from Page where parentID=0 order by pageOrder","get pages in page-edit_controller.php",$dev);
    }
?>