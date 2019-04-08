<?php
    $main=get_main_variation($_SESSION["product"]["itemID"]);
    echo open_script();
    echo    "var main_ID=".$main["variationID"].";";
    echo close_script();
    echo "<div id='wide_admin_form' class='left'>";
    echo    "<div id='feedback_message' class='admin_variation_panel left'>";
    echo    "</div>";
    echo    "<div id='variation_selector_panel' class='admin_variation_panel left'>";
    echo        "<span class='admin_vpanel_header left'>how does this product vary ? <span class='highlight'>! you must set all of these before creating variations !</span></span>";
    echo        product_vtype_form($variations);
    echo    "</div>";
    echo    "<div id='variation_panel' class='admin_variation_panel left'>";    
    echo        pvariation_form($variations);
    echo    "</div>";
    echo "</div>";
    echo "<div id='horizontal_instruction_panel' class='left'>";
    echo    "<p>At the top you can set a global price and postage calculation value that will be applied to all variations of this product. Only use the checkboxes to force the values if there will be no differences throughout the product variations.</p>";
    echo    "<p>The next panel down allows you to choose which of your variation types apply to this product.</p>";
    echo    "<p>Each product variation will need a value set for each variation type applied, for example, if you select variation types 'colour' and 'size' for this product you might make product variations of '10ft green', '5ft blue' and 10ft blue'</p>";
    echo    "<p>Selecting 'all' for any variation will create a variation for each of the values for that variation type, e.g. if you have sizes '2ft, 5ft and 10ft' and you select 'green' and 'all' then three variations will be created: '2ft green', '5ft green' and '10ft green'.</p>";
    echo    "<p>If you didn't set variation type values on your edit variations page you will have to provide a value for the variation on this page. There is no 'all' function in this case.</p>";
    echo "</div>";
?>