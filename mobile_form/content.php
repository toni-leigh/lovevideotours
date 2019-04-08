<?php
    $pages=site_query("select * from Page where parentID=0 order by pageOrder","content.php - get parent pages for top level navigation");
    echo "<nav>";
    echo dump_include(array("level"=>1,"include_name"=>"content.php [nav]"));
    echo    "<ul title='Main site navigation'>";
    while($nav_page=mysql_fetch_array($pages))
        get_pages($nav_page["pageID"],1,$page);
    echo    "</ul>";
    echo "</nav>";
    
    echo "<section id='content'>";
    echo dump_include(array("level"=>1,"include_name"=>"content.php [content]"));
    //echo "<h1>".$page["title"]."</h1>";
    if (authorised($page))
        if (isset($_GET["admin_divert"]))
            include $device."form/admin-".$item_type["itemType"].".php";
        else
            if ($page["justUserHTML"])
                echo $page["pageHTML"];
            else
                include $device."form/".$page["URL"].".php";
    else
        echo "You are not authorised to view this page";
    echo "</section>";
?>