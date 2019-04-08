<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $variations=explode("|",$_REQUEST["new_vars"]);
    $price=$_REQUEST["price_value"];
    $post_calc=$_REQUEST["post_value"];
    
    foreach ($variations as $variation)
    {
        // skip the erroneous extra array element that comes with the pipe split
        if ($variation!="")
        {
            // first assemble a variation checker array
            $vvalue_array=array();
            $vvalues=explode("&",$variation);
            foreach ($vvalues as $vvalue)
            {
                $pair=explode("=",$vvalue);
                $vvalue_array[$pair[0]]=$pair[1];
            }
            // use variation_exists() function to see if this avriation has been added already
            $variation_exists=variation_exists($vvalue_array);
            // if false            
            if ($variation_exists==0)
            {
                // add variation
                site_query("insert into ProductVariation (itemID,price,postCalc) values (".$_SESSION["product"]["itemID"].",".$price.",".$post_calc.")","ajax save_variations.php - insert pvariation");
                $variation_ID=mysql_insert_id();
                $vvalues=explode("&",$variation);
                foreach ($vvalues as $vvalue)
                {
                    //add the values to this variation
                    $pair=explode("=",$vvalue);
                    if ($pair[1]!="")
                    {
                        site_query("insert into ProductVariationValue (productVariationID,variationTypeID,variationValueID) values (".$variation_ID.",".$pair[0].",".$pair[1].")","ajax save_variations.php - insert pvariationvalues");
                    }
                }
            }
            else
                $message_append="<span class='ajax_message red left highlight'>some variations not added as they already existed</span>";
        }
    }
    
    $variations=get_product_variations(array("i_ID"=>$_SESSION["product"]["itemID"]));
    
    $html[0]=utf8_encode(pvariation_form($variations));
    $html[1]="<span class='ajax_message green left highlight'>variations added</span>".$message_append;
    
    echo json_encode($html);