<?php
    echo dump_include(array("level"=>"Cn-3","include_name"=>"products_controller.php"));
    if ($_POST["basket_submit"]) 
    {
        $b_params=array("i_ID"=>$_POST["item_ID"],"v_ID"=>$_POST["item_variation".$_POST["item_ID"]],"q"=>$_POST["quantity".$_POST["item_ID"]],"u_ID"=>$_POST["user_ID"]);
        $fail_quantity=bag_product($b_params);
    }
?>