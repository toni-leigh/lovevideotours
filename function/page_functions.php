<?php
    /*
     gets the current page name from the URL for loading specific page data
    */
    function get_page($page_ID="")
    {
        if (!is_numeric($page_ID))
            $page_query=site_query("select * from Page where URL='".$page_ID."'","get_page() - non numeric page ID");
        else
            $page_query=site_query("select * from Page where pageID=".$page_ID,"get_page() - numeric page ID");  
        return mysql_fetch_array($page_query);
    }    
    /*
     recursive function gets the pages from the site
     $root - is the top level parent ID when the function is initially called
    */
    function get_pages($root,$level,$current,$edit="")
    {
        $page=get_page($root);
        if ($page["pageDisplay"]==1&&authorised($page))
        {
            if ($edit=="edit")
            {
                echo "<a href='/page-edit/".$page["pageID"]."'>".$page["title"]."</a>";
            }
            else
            {
                echo "<a href='/".$page["URL"]."'>";
                if ($page["pageID"]==$current["pageID"])
                    echo "<span id='h".$page["pageID"]."nav' class='nav_sel left'>";
                else
                    echo "<span id='h".$page["pageID"]."nav' class='nav left'>";
                    
                if ($page["pageID"]==1) $title="Home";  
                elseif ($page["pageID"]==41) $title="Contact Us";                
                elseif ($page["pageID"]==42) $title="Prices";       
                elseif ($page["pageID"]==44) $title="Blog";
                elseif ($page["pageID"]==49) $title="About Us";
                else $title=$page["title"];
                
                echo $title;
                echo "</span>";
                echo "</a>";
            }
        }
        $count_subs=site_query("select * from Page where parentID=".$root." order by pageOrder","get_pages() - count subs query");
        if (mysql_num_rows($count_subs)>0)
        {
            $level=$level+1;
            while ($sub=mysql_fetch_array($count_subs))
                get_pages($sub["pageID"],$level,$current);
        }
    }
?>