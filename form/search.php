<?php
    echo "<div id='search_form' class='left'>";
    echo    dump_include(array("level"=>2,"include_name"=>"search.php"));
    echo    "<form method='post' action='/search-results'>";
    echo       "<input id='search_input' class='left' type='text' name='search_term' value='".$_POST["search_term"]."'/>";
    echo       "<input id='search_submit' type='image' src='/img/h_search_icon.png' alt='search the site'/>";
    echo    "</form>";
    echo "</div>";
?>