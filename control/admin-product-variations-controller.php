<?php
    $_SESSION["product"]=$item;
    $variations=get_product_variations(array("i_ID"=>$item["itemID"],"in_stock"=>0));
?>