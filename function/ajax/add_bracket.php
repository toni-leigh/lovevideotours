<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../postage_functions.php';
    
    $calc_type=$_REQUEST["calc_type"];
    
    $value_pairs=explode("&",$_REQUEST["inputs"]);
    $min_value=0;
    $max_value=0;
    foreach ($value_pairs as $value_pair)
    {
        $values=explode("=",$value_pair);
        $input_key=$values[0];
        $input_value=$values[1];
        if (strpos($input_key,"min_value"))
        {
            $min_value=$input_value;
        }
        elseif (strpos($input_key,"max_value"))
        {
            $max_value=$input_value;
            if ($max_value==""||$max_value=="MAX")
                $max_value=-1;
        }
        elseif (strpos($input_key,"cost"))
        {
            // build a variation value string            
            
            $reference=str_replace("cost","",$input_key);
            if (is_numeric($reference))
            {
                site_query("update PostageCharges set minValue=".$min_value.", maxValue=".$max_value.", standardDelivery=".$input_value." where postageChargeID=".$reference,"update postage charge in add_bracket.php");
                
                // get the variation value ID
                
                //update with the variation value string
            }
            else
            {
                if (is_numeric($min_value)&&is_numeric($max_value)&&is_numeric($input_value))
                {
                    // insert a variation value using the user ID and the users postage calc variation type value
                    
                    //get the last insert ID
                    
                    //set the last insert ID in this query as the reference
                    site_query("insert into PostageCharges (userID,minValue,maxValue,postageCalcType,standardDelivery) values (".$_SESSION["user"]["userID"].",".$min_value.",".$max_value.",'".$calc_type."',".$input_value.")","insert postage charge in add_bracket.php");
                }
            }
        }
    }
    $brackets=get_postages(array("bracket"=>$calc_type,"user_ID"=>$_SESSION["user"]["userID"]));
    echo json_encode(bracket_updater(array("brackets"=>$brackets,"calc_type"=>$calc_type)));