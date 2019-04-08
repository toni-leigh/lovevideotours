<?php
    //gets this session users variation types
    function get_variation_types()
    {
        $vts="select * from VariationType where userID=".$_SESSION["user"]["userID"]." order by variationTypeName asc";
        $vtq=site_query($vts,"get_variation_types()");
        return $vtq;
    }
    //builds the checkbox set for choosing variation types that apply to a product
    function product_vtype_form($variations)
    {
        // if any variations set then all disabled
        $vcount=mysql_num_rows($variations);
        // this users and this products vtypes
        $variation_types=get_variation_types();
        $pvtypes=get_product_variation_types("variationTypeID");
        // build vtype selector output
        $vtf="";
        $vtf.="<span class='vinstock_main right'>&nbsp;";  
        $vtf.="</span>";
        while ($vtype=mysql_fetch_array($variation_types))
        {
            $vvalues=get_variation_values($vtype["variationTypeID"]);
            $vtype_name=$vtype["variationTypeName"];
            if (mysql_num_rows($vvalues)>0||is_perm($vtype_name))
            {
                // floats the price and postage ones right, others left
                if (is_perm($vtype_name)) $float=" right permcheck"; else $float=" left ";
                if (in_array($vtype["variationTypeID"],$pvtypes)) $checked=" checked "; else $checked="";
                if (is_perm($vtype_name)||$vcount>0) $disabled=" disabled='disabled' "; else $disabled="";            
                $vtf.="<span class='vtype_selector ".$float."'>";
                $vtf.="<input id='".$vtype["variationTypeID"]."_vtype' type='checkbox' ".$checked." ".$disabled." onclick='set_vtype_output(\"".$vtype["variationTypeID"]."\")'/>".$vtype["variationTypeName"];
                $vtf.="</span>";
            }
        }
        return $vtf;
    }
    function is_perm($vtype_name)
    {
        if ($vtype_name=="price"||$vtype_name=="post calc"||$vtype_name=="pack quantity")
            return 1;
        else
            return 0;
    }
    //gets the variations for this product
    function get_product_variation_types($key=null)
    {
        $pvtypes=array();
        $pvts="select * from ProductVariationType, VariationType where productID=".$_SESSION["product"]["itemID"]." and ProductVariationType.variationTypeID=VariationType.variationTypeID and removed=0 order by variationTypeName asc";
        $pvtq=site_query($pvts,"get_product_variation_types()");
        //get this products variation types into array
        if ($key==null)
            while ($pvtype=mysql_fetch_array($pvtq)) $pvtypes[]=$pvtype;
        else
            while ($pvtype=mysql_fetch_array($pvtq)) $pvtypes[]=$pvtype[$key];
        return $pvtypes;
    }
    //shows the variation form for a product
    function pvariation_form($variations)
    {
        $pvf="";
        
        // we need to get all the variation types for each row, so they can be switched on and off
        $v_types=get_variation_types();
        $vtypes=array();
        while ($vtype=mysql_fetch_array($v_types))
        {
            $vtypes[$vtype["variationTypeName"]]["values"]=get_variation_values($vtype["variationTypeID"]);
            $vtypes[$vtype["variationTypeName"]]["details"]=$vtype;
        }
        $product_variation_types=get_product_variation_types("variationTypeName");
        $pvtypes=get_product_variation_types("variationTypeName");        
        
        // output the variation rows, no need if none set yet
        if (mysql_num_rows($variations)>0)
        {
            $counter==0;
            variation_header_row($vtypes,$pvtypes);
            $pvf.="<form id='variation_editor'>";
            while ($variation=mysql_fetch_array($variations)) //loops out the variations
            {
                $counter++;
                $pvf.=variation_row(array("vtypes"=>$vtypes,"pvtypes"=>$pvtypes,"variation"=>$variation,"counter"=>$counter));
            }
            $pvf.="</form>";
            $pvf.="<span id='save_variations_button' class='button right' onclick='save_variations()'>save edited variations</span>";
            $pvf.="<span id='changes_made' class='highlight red right'>! changes made ! click save - &gt;</span>";
        }
        
        // output the variation adder row        
        variation_header_row($vtypes,$pvtypes);
        $pvf.=variation_row(array("vtypes"=>$vtypes,"pvtypes"=>$pvtypes,"variation"=>null));
        $pvf.="<span id='add_variations_button' class='button right' onclick='add_variations(\"".$_SESSION["user"]["pvTypePrice"]."\",\"".$_SESSION["user"]["pvTypePost"]."\")'>add variations</span>";
        
        // initial variation preview panel - this will be updated via ajax
        $pvf.="<div id='variation_preview' class='left'>";
        $pvf.=variation_preview(array("selections"=>array()));
        $pvf.="</div>";
        
        // return HTML
        return $pvf;
    }
    function variation_header_row($vtypes,$pvtypes)
    {
        // output the header, including on off hiddens and classes
        $vhr.="<span id='variation_header_row' class='variation_row left'>";
        $vhr.="<span class='vinstock_main right'>&nbsp;";  
        $vhr.="</span>";
        while (list($key,$value) = each($vtypes)) //all of the variation types with their values, for the header
        {            
            $vtype_ID=$vtypes[$key]["details"]["variationTypeID"];
            $vtype_name=$vtypes[$key]["details"]["variationTypeName"];
            
            // sets classes, price and postage right and hides
            if (is_perm($vtype_name)) $float=" right perm "; else $float=" left ";
            if (in_array($key,$pvtypes)) $class=""; else $class=" hidden ";
            
            // outputs header
            $vhr.="<span class='".$vtype_ID."vtype pvariation_header ".$class." ".$float."'>".$vtypes[$key]["details"]["variationTypeName"]."</span>";
        }
        $vhr.="</span>";
    }
    function variation_row($in)
    {
        $pvvalues=array();
        $in_stock=$in["variation"]["inStock"];
        $main=$in["variation"]["main"];
        if ($in["variation"]==null) $variation_adder=1;
        if ($variation_adder) //null variation is the add new variants row
        {
            $vr.="<span id='variation_defintion_row' class='left'>";
            $select_js="='update_output_panel()'";
        }
        else
        {
            $select_js="='show_message()'";
            //also get the product variation values - so we can set the form values correctly         
            $pvariation_values=get_product_variation_values($in["variation"]["variationID"]);
            while ($pvariation_value=mysql_fetch_array($pvariation_values))
            {
                if ($pvariation_value["variationValueID"]==0)
                    $pvvalues[$pvariation_value["variationTypeID"]]=$pvariation_value["undefinedValue"];
                else
                    $pvvalues[$pvariation_value["variationTypeID"]]=$pvariation_value["variationValueID"];
            }
            $add_classes="";
            if ($main==1)
            {
                
                if ($in_stock==1) $add_classes.=" main_row ";
                else $add_classes.=" main_row_out ";
            }
            else
            {
                if ($in_stock==1) $add_classes.=" instock_row "; else $add_classes.=" outstock_row ";
            }
            $vr.="<span id='".$in["variation"]["variationID"]."variation' class='variation_row ".$add_classes." left'>";
        }
        if ($variation_adder) $vr.="<form id='variation_adder'>";
        // for all variation rows apart from the adder we need to give in stock and main functionality
        if (!$variation_adder)
        {
            $vr.="<span class='vinstock_main right'>";
            $vr.="<span class='vinstock'>";
            if ($in_stock==1) $checked=" checked='checked' "; else $checked="";
            $vr.="<input id='".$in["variation"]["variationID"]."=instock' class='".$in["variation"]["variationID"]."instock' name='".$in["variation"]["variationID"]."=instock' type='checkbox' ".$checked."/ onclick='mark_row(\"".$in["variation"]["variationID"]."\")'>";
            $vr.="</span>";
            $vr.="<span class='vmain'>";
            if ($main==1) $checked=" checked "; else $checked="";
            $vr.="<input id='".$in["variation"]["variationID"]."=main' class='".$in["variation"]["variationID"]."main' name='main' value='main=".$in["variation"]["variationID"]."' type='radio' ".$checked." onclick='mark_main(\"".$in["variation"]["variationID"]."\")'/>";
            $vr.="</span>";
            $vr.="</span>";
        }
        else
        {
            $vr.="<span class='vinstock_main right'>&nbsp;";  
            $vr.="</span>";
        }
        while (list($key,$value) = each($in["vtypes"])) //all of the variation types with their values
        {
            $vtype_ID=$in["vtypes"][$key]["details"]["variationTypeID"];
            $vtype_name=$in["vtypes"][$key]["details"]["variationTypeName"];
            
            //only show if in the product variation types
            if (in_array($key,$in["pvtypes"])) $class=""; else $class=" hidden ";
                
            // floats the price and postage ones right, others left
            if (is_perm($vtype_name)) $float=" right perm"; else $float=" left ";
            
            // if it is noth the variation adder then we need the variation ID to for the save function, = is used as a seperator to easily split query string values in ajax
            if (!$variation_adder) $extra_input_name=$in["variation"]["variationID"]."=";
                
            // add each variation type element to the row
            $vr.="<span class='".$vtype_ID."vtype pvariation_input".$class." ".$float."'>";
            if (mysql_num_rows($in["vtypes"][$key]["values"])>0)
            {
                // if this is the variation adder then multiple is available as an option
                if ($variation_adder&&$vtype_name!="pack quantity") $multiple=" multiple='multiple' size='8' "; else $multiple="";
                                
                $vr.="<select id='".$vtype_ID."' name='".$extra_input_name.$vtype_ID."' ".$multiple." onchange".$select_js.">";
                //if ($variation_adder) $vr.="<option name='all'>all</option>";
                mysql_data_seek($in["vtypes"][$key]["values"],0);
                while ($vvalue=mysql_fetch_array($in["vtypes"][$key]["values"]))
                {
                    if ($pvvalues[$vtype_ID]==$vvalue["variationValueID"])
                        $selected=" selected='selected' ";
                    else
                        $selected="";
                    if (!$variation_adder) $option_value=" value='".$vvalue["variationValueID"]."' ";
                    $vr.="<option ".$option_value." name='".$vvalue["variationValueID"]."' ".$selected.">".$vvalue["variationValue"]."</option>"; //[".$pvvalues[$vtype_ID]."]{".$vvalue["variationValueID"]."}
                }
                $vr.="</select>";
            }
            else
            {
                // text ones are just boxes
                if ($variation_adder)
                    $vr.="<input id='".$vtype_ID."value_add' class='variation_text_field text_field left' type='text' name='".$vtype_ID."value' value='0' onkeyup='update_preview_text(\"".$vtype_ID."\",\"".$vtype_name."\")'/>";
                else
                {
                    if ($vtype_ID==$_SESSION["user"]["pvTypePrice"]) $vvalue=$in["variation"]["price"];
                    elseif ($vtype_ID==$_SESSION["user"]["pvTypePost"]) $vvalue=$in["variation"]["postCalc"];
                    $vr.="<input id='".$vtype_ID."' class='variation_text_field text_field left' type='text' name='".$extra_input_name.$vtype_ID."' value='".$vvalue."' onkeyup='show_message()'/>";
                }
            }
            $vr.="</span>";
        }
        if ($main==1) $hidden_remove=" hidden ";
        $vr.="<span class='vremove right'>";
        $vr.="<input id='".$in["variation"]["variationID"]."=remove' class='".$in["variation"]["variationID"]."remove ".$hidden_remove." remove_checkbox' name='".$in["variation"]["variationID"]."=remove' type='checkbox' onclick='mark_row(\"".$in["variation"]["variationID"]."\")'/>";
        $vr.="</span>";
        if ($variation_adder) $vr.="</form>";
        $vr.="</span>";
        if ($in["counter"]%5==0)
            $vr.="<span class='variation_row_divider'>&nbsp;</span>";
        return $vr;
    }
    function variation_preview($in=array())
    {
        $vpr="";
        $pvtypes=get_product_variation_types();
        //count dynamic types
        $counter=0;
        foreach ($pvtypes as $pvtype)
        {
            $pvtypes[$counter]["values"]=get_variation_values($pvtype["variationTypeID"]);
            $counter++;
        }
        $vpr.=preview_rows(array("pvtypes"=>$pvtypes,"current"=>0,"row_starter"=>"","row_string"=>"","type_name_prefixes"=>array(),"type_value_prefixes"=>array(),"selections"=>$in["selections"]));
        return $vpr;
    }
    function preview_rows($in)
    {        
        // find out the value count for this recursion
        if (isset($in["pvtypes"][$in["current"]]["values"]))
            $value_count=mysql_num_rows($in["pvtypes"][$in["current"]]["values"]);
        else
            $value_count=0;
            
        // set the prefix array to include this recursion, used for output
        $in["type_name_prefixes"][$in["current"]]=$in["pvtypes"][$in["current"]];
        if ($value_count>0)
        {
            // then the recursion needs to process all the values
            mysql_data_seek($in["pvtypes"][$in["current"]]["values"],0);
            while ($value=mysql_fetch_array($in["pvtypes"][$in["current"]]["values"]))
            {
                // prepare the selections array ready to output based on selection
                if (isset($in["selections"][$in["pvtypes"][$in["current"]]["variationTypeID"]]))
                    $selections=$in["selections"][$in["pvtypes"][$in["current"]]["variationTypeID"]];
                else
                    $selections=array();
                    
                /*$in["row_string"].="Cpv:".$in["pvtypes"][$in["current"]]["variationTypeID"].":<br/>Npv".$in["pvtypes"][$in["current"]+1]["variationTypeID"].":<br/>";
                $in["row_string"].="CSel:".$in["selections"][$in["pvtypes"][$in["current"]]["variationTypeID"]].":<br/>NSel".$in["selections"][$in["pvtypes"][$in["current"]+1]["variationTypeID"]]."<br/><br/>";*/
                // only build a row if the selections made allow it
                if (in_array($value["variationValue"],$selections))
                    $build_row=1;
                else
                    $build_row=0;
                // build the row or recurse
                if ($build_row)
                { 
                    if (is_array($in["pvtypes"][$in["current"]+1])&&count($in["selections"][$in["pvtypes"][$in["current"]+1]["variationTypeID"]])>0)
                    {
                        // if this product variation type is not the last one then add this to prefixes and recurse to get next level of values
                        // also only recurse if the next selection has selections in it to add those selections to the row, we dont want an empty selection to remove all rows
                        $in["type_value_prefixes"][$in["current"]]=$value;
                        $in["row_string"].=preview_rows(array("pvtypes"=>$in["pvtypes"],"current"=>($in["current"]+1),$in["row_string"],"type_name_prefixes"=>$in["type_name_prefixes"],"type_value_prefixes"=>$in["type_value_prefixes"],"selections"=>$in["selections"]));
                    }
                    else
                    {
                        // output a row, as we are at the leaves of the structure
                        $in["row_string"].=preview_row(array("type_name_prefixes"=>$in["type_name_prefixes"],"value"=>$value,"current"=>$in["current"],"type_value_prefixes"=>$in["type_value_prefixes"],"pvtype"=>$in["pvtypes"][$in["current"]]));
                    }
                }
            }
            // output breaks between rows, for ease of read (note the > values are plus 3 for price, postage and pack quantity)
            // we only do this on multival rows
            if ($in["current"]==1&&count($in["pvtypes"])>4) $in["row_string"].="<span class='preview_break left'>&nbsp;</span>";
            if ($in["current"]==2&&count($in["pvtypes"])>5) $in["row_string"].="<span class='preview_break_feint left'>&nbsp;</span>";
        }
        else
        {
            // or just the value for text as there is not a finite number of values for text
            if (is_array($in["pvtypes"][$in["current"]+1]))
                $in["row_string"].=preview_rows(array("pvtypes"=>$in["pvtypes"],"current"=>($in["current"]+1),$in["row_string"],"type_name_prefixes"=>$in["type_name_prefixes"],"type_value_prefixes"=>$in["type_value_prefixes"],"selections"=>$in["selections"]));
            else
                $in["row_string"].=preview_row(array("type_name_prefixes"=>$in["type_name_prefixes"],"value"=>"","current"=>$in["current"],"type_value_prefixes"=>$in["type_value_prefixes"],"pvtype"=>$in["pvtypes"][$in["current"]]));
        }
        return $in["row_string"];
    }
    function preview_row($in)
    {
        // this is a query string, sent via hidden field for saving the variation - it is built up during row output and is easier to loop over than a single set of form fields
        $qs="";
        $vr="<span class='variation_preview_row left'>";
        $variation_check=array();
        // for loop goes over all the previous set values to output the whole row
        for ($counter=0;$counter<=$in["current"];$counter++)
        {
            // get the loop specific values for this output
            $vtype_ID=$in["type_name_prefixes"][$counter]["variationTypeID"];
            $vtype_name=$in["type_name_prefixes"][$counter]["variationTypeName"];
            $vvalue=$in["type_value_prefixes"][$counter]["variationValue"];
            $vvalue_ID=$in["type_value_prefixes"][$counter]["variationValueID"];
            
            // update the check array to mark this variation as already there
            $variation_check[$vtype_ID]=$vvalue_ID;
            
            // update the query string with this value pair - NB on the last iteration $vvalue will be missing and added at the end of the function
            $qs.=$vtype_ID."=".$vvalue_ID;
            
            // float the price and postage right, rest left
            if (is_perm($vtype_name)) $float=" right perm "; else $float=" left ";
            
            //format price as price
            if ($vtype_name=="price") $vvalue=format_price($vvalue);
            
            // create the preview value output, type_name:type_value
            $vr.="<span class='preview_full_value ".$float."'>";
            $vr.="<span class='preview_vtype ".$vtype_ID."pr_vtype'>".$vtype_name."&nbsp;:&nbsp;</span>";
            if ($counter<$in["current"])
            {
                // the value is held in the prefix array
                $vr.="<span class='preview_value ".$vtype_ID."pr_val_pvtype'>".$vvalue."</span>";
                $vr.="</span>";
                
                // also append & to query string ready for next iteration
                $qs.="&";
            }
        }
        // finish off the query string, ready for the hidden field
        $qs.=$in["value"]["variationValueID"];
        
        // finish off check array        
        $variation_check[$vtype_ID]=$in["value"]["variationValueID"];
        
        // the last value comes in as 'value'
        $vr.="<span class='preview_value ".$vtype_ID."pr_val_pvtype'>".$in["value"]["variationValue"]."</span>";
        if (variation_exists($variation_check)) $vr.="<span class='v_exists'>exists !</span>";
        $vr.="<input class='add_these' type='hidden' value='".$qs."'/>";
        $vr.="</span>";
        $vr.="</span>";
        return $vr;
    }
    //gets the variation values for a given type
    function get_variation_values($vtype_ID)
    {
        $vvs="select * from VariationValue where variationTypeID=".$vtype_ID." and removed=0 order by variationValue";
        $vvq=site_query($vvs,"get_variation_values()");
        return $vvq;
    }
    //shows a form for adding a new variation type
    function variation_type_form()
    {
        $vf="<div id='main_form' class='left'>";
        $vf.="<form method='post' action=''>";
        $vf.="<input type='hidden' name='vtype_sub'/>";
        $vf.="<input id='variation_type' class='text_field left' type='text' name='variation_type' value=''/>";
        $vf.="<input id='variation_type_submit' class='submit_button button right' type='submit' name='submit' value='add variation type'/>";
        $vf.="</form>";
        $vf.="</div>";
        return $vf;
    }
    //shows the input for adding a new variation value
    function variation_value_input($vtype_ID)
    {
        $vvi="<span class='button right' onclick='save_new_vvalue(\"".$vtype_ID."\")'>add new value</span>";
        $vvi.="<input id='".$vtype_ID."variation_value' class='vvalue_field text_field right' type='text' name='".$vtype_ID."variation_value' value=''/>";
        return $vvi;
    }
    //builds the form for setting the master values to be used product wide on variations
    function master_variation_form()
    {
        $product=$_SESSION["product"];        
        $mvf="<span class='admin_vpanel_header left'>optional: set main values, use this if there is just one variation.</span>";
        $mvf.="<form id='master_variation_form' class='right'>";
        if ($product["masterPrice"])
        {
            $mvf.="<input id='master_price' class='text_field left' type='text' name='master_price' value='".$product["masterPrice"]."'/>";
            $mvf.="<input id='use_master_price' class='left' type='checkbox' checked='checked' onclick='set_master_price()'/>";
        }
        else
        {
            $mvf.="<input id='master_price' class='text_field left' type='text' name='master_price' value=''/>";
            $mvf.="<input id='use_master_price' class='left' type='checkbox' onclick='set_master_price()'/>";
        }
        if ($product["masterPostage"])
        {
            $mvf.="<input id='master_postage' class='text_field left' type='text' name='master_price' value='".$product["masterPostage"]."'/>";
            $mvf.="<input id='use_master_postage' class='left' type='checkbox' checked='checked' onclick='set_master_postage()'/>";
        }
        else
        {
            $mvf.="<input id='master_postage' class='text_field left' type='text' name='master_price' value=''/>";
            $mvf.="<input id='use_master_postage' class='left' type='checkbox' onclick='set_master_postage()'/>";
        }
        $mvf.="</form>";
        return $mvf;
    }
    //displays the variation values on the edit variation screen
    function vtype_values($vtype_ID)
    {
        $variation_values=get_variation_values($vtype_ID);
        $vv="";
        if (mysql_num_rows($variation_values)>0)
        {
            while ($vvalue=mysql_fetch_array($variation_values))
            {
                $vv.="<div class='vvalue_row left'>";
                $vv.=$vvalue["variationValue"]." <span class='remove_vvalue' onclick='remove_vvalue(\"".$vtype_ID."\",\"".$vvalue["variationValueID"]."\")'><img class='right' src='/img/remove16.png' alt='remove variation'/></span>";
                $vv.="</div>";
            }
        }
        else
        {
            $vv.="<div class='vvalue_row left'>";
            $vv.="no values set - you will be asked to create these manually when using this variation on a product <span class='highlight'> - it is strongly advised that you create all values for a variation if there is a finite number of values</span>";
            $vv.="</div>";  
        }
        return $vv;
    }
    /*
        gets the actual saved variations of a given product, also used to display the variations front stage
    */
    function get_product_variations($in)
    {
        if ($in["in_stock"])
            $in_stock=" and inStock=1 ";
        $vs="select * from ProductVariation where itemID=".$in["i_ID"].$in_stock." and removed=0";
        $vq=site_query($vs,"get_product_variations()");
        return $vq;
    }
    /*
        get the stored values for this variation, the set that defines the variation
    */
    function get_product_variation_values($variation_ID)
    {
        $vs="select * from ProductVariationValue where productVariationID=".$variation_ID;
        $vq=site_query($vs,"get_product_variation_values()");
        return $vq;
    }
    /*
        used to see if a variation already exists for this product
        $in is an array with the product variation type as the key and the value at this position being the variation value
    */
    function variation_exists($in)
    {
        // build the comparison string based on the in pairs (and count them for the comparison)
        $comp="";
        $count=0;
        while (list($key,$value) = each($in))
        {
            if ($value!="")
            {
                $count++;
                $comp.="(variationTypeID=".$key." and variationValueID=".$value.") or ";
            }
        }        
        // then loop through all variations using the comparison bit to see if there is a match in the variations
        $variations=get_product_variations(array("i_ID"=>$_SESSION["product"]["itemID"]));
        while ($variation=mysql_fetch_array($variations))
        {
            if (count($in))
            {
                $ves="select * from ProductVariationValue where productVariationID=".$variation["variationID"]." and (";
                if (strlen($comp))
                {
                    $ves.=$comp;
                    $ves=substr($ves,0,-4).")";
                }
                else
                {
                    $ves=substr($ves,0,-5);
                }
                $veq=site_query($ves,"variation_exists() - v_ID: ".$variation["variationID"]);
                
                // if we hit a match then break back to the add variation with a 'variation found' value
                if (mysql_num_rows($veq)==$count) return 1;            
            }
        }
        return 0;
    }
?>