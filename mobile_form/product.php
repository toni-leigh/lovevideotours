<?php
    echo dump_include(array("level"=>3,"include_name"=>"product.php"));
    if (is_array($item))
    {
        echo basket_button(array("product"=>$item,"err"=>$fail_quantity));
    }
    else
    {
        echo product_list(array("products"=>$items,"width"=>4,"basket"=>1));
    }
?>