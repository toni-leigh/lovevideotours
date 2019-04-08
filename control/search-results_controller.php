<?php
    // relocate with URL values for share
    if (isset($_POST["search_term"])) header("location:/search-results/".urlencode($_POST["search_term"]));
    // if relocated then reset post value for the rest
    if (isset($_GET["search_term"]))
    {
        $_POST["search_term"]=urldecode($_GET["search_term"]);
        site_query("insert into SearchString (searchString) values ('".$_POST["search_term"]."')","save search string");
    }
    
    // get results
    $results=perform_item_search($_POST["search_term"]);
    $map_items=get_video_items(array("search_term"=>$_POST["search_term"],"map"=>1));
    if (mysql_num_rows($results)>0)
    {
        $phrase_heading=1;
    }
    else
    {
        $term_array=return_term_array($_POST["search_term"]);
        dev_dump($term_array,"Term Array",$dev);
        $term_count=count($term_array);
        $counter=1;
        $term_string="";
        foreach ($term_array as $term)
        {
            $term_string=$term_string."<span class='search-term-hightlight'>'".$term."'</span>";
            if ($term_count==$counter)
                $term_string=$term_string."";
            else
                if (($term_count-1)==$counter)
                    $term_string=$term_string." or ";
                else
                    $term_string=$term_string.", ";
            $counter=$counter+1;            
        }
        $results=perform_item_search($term_array);
        $map_items=get_video_items(array("search_term"=>$term_array,"map"=>1));
    }
?>