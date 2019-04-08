<?php
    if ($phrase_heading)
    {
        echo "<h1>";
        echo    "<div class='l_heading'>";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -435px;'></span><span class='l_heading_text'>Search Results</span></span>";
        echo    "</div>";
        echo "</h1>";
        echo "<p>The following results included the entire phrase <span class='search-term-hightlight'>'".$_POST["search_term"]."'</p>";
        echo "<p>searching for a town will reveal places near to that town; searching for a county will reveal all places in that county</p>";
    }
    else
    {
        echo "<h1>";
        echo    "<div class='l_heading'>";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -435px;'></span><span class='l_heading_text'>Search Results</span>";
        echo    "</div>";
        echo "</h1>";
        echo "<p>The following results included at least one of the follow terms: ".$term_string."</p>";
        echo "<p>searching for a town will reveal places near to that town; searching for a county will reveal all places in that county</p>";
    }
    if (mysql_num_rows($results)>0)
    {
        echo item_list($results);
        echo vi_map(array("map_items"=>$map_items));
    }
    else
    {
        echo "<div id='no_results'>";
        echo    "<p>We're sorry, but your search has not resulted in any results. Try again, or maybe you could visit one of our category pages:</p>";
        echo    "<p><a href='/things-to-do'>things to do</a></p>";
        echo    "<p><a href='/places-to-stay'>places to stay</a></p>";
        echo "</div>";
    }
?>