<?php
    $dev=1;
    $results=perform_item_search($_POST["search_term"]);
    if (mysql_num_rows($results)>0)
    {
        echo "<h1>The following results included the entire phrase <span class='search-term-hightlight'>'".$_POST["search_term"]."'</span></h1>";
        dev_dump_query($results,"phrase search query dump",$dev);
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
        echo "<h1>The following results included at least one of the follow terms: ".$term_string."</h1>";
        echo "<span class='full-screen-width'>(the whole phrase <span class='search-term-hightlight'>'".$_POST["search_term"]."'</span> was not found)</span>";
        $results=perform_item_search($term_array);
        dev_dump_query($results,"split term search query dump",$dev);
    }
?>