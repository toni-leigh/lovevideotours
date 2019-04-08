<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $new_vals=explode("&",str_replace("%3D","=",$_REQUEST["inputs"]));
    $pvtypes=get_product_variation_types("variationTypeID");
    $vvalues=array();
    
    // build a variation value array
    foreach ($new_vals as $new_val)
    {
        // get the first variation ID
        $vvalue=array();
        $vvalue=explode("=",$new_val);
        $variation_ID=$vvalue[0];
        $vtype_ID=$vvalue[1];
        $vvalue=$vvalue[2];
        // values for updating
        if (in_array($vtype_ID,$pvtypes))
        {
            $vvalues[$variation_ID]["v_ID"]=$variation_ID;  
            $vvalues[$variation_ID]["values"][$vtype_ID]=$vvalue;
        }
        //instock
        if ($vtype_ID=="instock")
        {
            if ($vvalue=="on")
                $vvalues[$variation_ID]["inStock"]=1;
        }
        //remove
        if ($vtype_ID=="remove")
        {
            if ($vvalue=="on")
                $vvalues[$variation_ID]["remove"]=1;
        }
        
        // values jumbled because of stupid radio box standard
        if ($variation_ID=="main") $vvalues[$vvalue]["main"]=1;
        
        // values for checking
        if (in_array($vtype_ID,$pvtypes)&&$vtype_ID!=$_SESSION["user"]["pvTypePrice"]&&$vtype_ID!=$_SESSION["user"]["pvTypePost"])
            $vchecks[$variation_ID][$vtype_ID]=$vvalue;
    }
    // now save
    foreach ($vvalues as $vvalue)
    {
        //get the variation_ID
        $variation_ID=$vvalue["v_ID"];
        // in stock and main values
        if (isset($vvalue["main"])) $main=1; else $main=0;
        if (isset($vvalue["inStock"])) $in_stock=1; else $in_stock=0;
        if (isset($vvalue["remove"])) $remove=1; else $remove=0;
        
        // use variation_exists() function to see if this avriation has been added already
        $variation_exists=variation_exists($vchecks[$variation_ID]);
        
        // must always update price and post calc, only if numeric
        if (is_numeric($vvalue["values"][$_SESSION["user"]["pvTypePrice"]])&&is_numeric($vvalue["values"][$_SESSION["user"]["pvTypePost"]]))
            site_query("update ProductVariation set main=".$main.", inStock=".$in_stock.", price=".$vvalue["values"][$_SESSION["user"]["pvTypePrice"]].", postCalc=".$vvalue["values"][$_SESSION["user"]["pvTypePost"]].", removed=".$remove." where variationID=".$variation_ID,"update product variation in ajax update_variations.php");
        
        // if false            
        if ($variation_exists==0)
        {
            while (list($key,$value) = each($vvalue["values"]))
            {
                if ($value!=""&&in_array($key,$pvtypes))
                    site_query("update ProductVariationValue set variationValueID=".$value." where productVariationID=".$variation_ID." and variationTypeID=".$key,"update product variation value in ajax update_variations.php");
            }
        }
    }
    
    $variations=get_product_variations(array("i_ID"=>$_SESSION["product"]["itemID"]));
    
    $html[0]=utf8_encode(pvariation_form($variations));
    $html[1]="<span class='ajax_message green highlight left'>variations updated</span>";
    
    echo json_encode($html);