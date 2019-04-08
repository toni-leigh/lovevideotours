<?php
    echo dump_include(array("level"=>3,"include_name"=>"product.php"));
    // if the item is defined show item page / else show items (if items is not defined then there is an error with category in the URL, such as URL tampering)
    if (is_array($item))
        echo product_page($item);
    else
        if (isset($items))
            echo product_list(array("products"=>$items,"width"=>3,"basket"=>1,"depth"=>3));
        else
            echo entity_not_found();
?>