<?php
    echo "<h2>Your Variations</h2>";
    echo "<div id='instruction_panel' class='right'>";
    echo    "<p>On this page you create the types of variations that your products might have. For example, if you sell clothes then the variation type might be 'size' and the values might be '8, 10, 12 ...' or 'S, M, L ...'. Likewise, if your product comes in different colours, you might have a variation type 'colour' and values 'red, green, blue and yellow'</p>";
    echo    "<p>Keep variation definitions seperate, so if you sell something that is available in sizes S-M and four different colours, you would define two variations seperately. They can then be merged when applying them to a product.</p>";
    echo    "<p>It is not necessary to specify values for a variation type, if you don't you will be prompted for a value when adding a variation to a product.</p>";
    echo    "<p>When you come to create a product you will asked what variations of that product are available for sale using the values that you set on this page.</p>";
    echo    "<p>It is expected that for most merchants, particularly those with established lines, the variations will be set and then left, without the need to constantly change them. Merchants with unusual or highly varied stock may need to spend time establishing and revisiting their variations.</p>";
    echo "</div>";
    echo variation_type_form();
    echo "<div id='main_admin_panel' class='left'>";
    while ($variation_type=mysql_fetch_array($variation_types))
    {
        $vtype_ID=$variation_type["variationTypeID"];
        echo "<div class='vtype_row left'>";
        echo    "<div class='vtype_heading left'>";
        echo        "<span class='vtype_name left'>".$variation_type["variationTypeName"]."</span>";
        echo        "<span class='vtype_new_value right'>".variation_value_input($vtype_ID)."</span>";
        echo    "</div>";
        echo    "<div id='vtype_values".$vtype_ID."'>";
        echo        vtype_values($vtype_ID);
        echo    "</div>";
        echo "</div>";
    }
    echo "</div>";
?>