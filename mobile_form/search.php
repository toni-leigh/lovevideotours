<?php
    echo "<div id='search_form'>";
    echo    dump_include(array("level"=>2,"include_name"=>"search.php"));
    echo    "<form method='post' action='/search-results'>";
    echo       "<input type='text' name='search_term' value='".$_POST["search_term"]."'/>";
    echo       "<input id='search_submit' class='submit_button button' type='submit' name='submit' value='Search'/>";
    echo    "</form>";
    echo "</div>";
?>