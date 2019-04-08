<?php
    //retrieve last order for addresses
    $last_order_query=site_query("select * from UserOrder where basketID='".$_SESSION["basket_ID"]."' order by orderID desc limit 1","order_address.php - last order query",$dev);
    $last_order=mysql_fetch_array($last_order_query);
    if (isset($_POST["addresses_submitted"]))
    {
        //check for errors
        $errors=array();
        $field_lengths=array();
        $field_lengths["primary_email"]=255;$field_lengths["billing_first_name"]=20;$field_lengths["billing_last_name"]=20;
        $field_lengths["billing_street1"]=100;$field_lengths["billing_street2"]=100;$field_lengths["billing_city"]=40;
        $field_lengths["billing_postal_code"]=10;$field_lengths["delivery_first_name"]=20;$field_lengths["delivery_last_name"]=20;
        $field_lengths["delivery_street1"]=100;$field_lengths["delivery_street2"]=100;$field_lengths["delivery_city"]=40;
        $field_lengths["delivery_postal_code"]=10;        
        $accepted_chars=array();
        $accepted_chars["primary_email"]=array("@","-",".","_");$accepted_chars["billing_first_name"]=array("-",".","'"," ");
        $accepted_chars["billing_last_name"]=array("-",".","'"," ");$accepted_chars["billing_street1"]=array("-",".","'"," ",",","/");
        $accepted_chars["billing_street2"]=array("-",".","'"," ",",","/");$accepted_chars["billing_city"]=array("-",".","'"," ");
        $accepted_chars["billing_postal_code"]=array("-"," ");$accepted_chars["delivery_first_name"]=array("-",".","'"," ");
        $accepted_chars["delivery_last_name"]=array("-",".","'"," ");$accepted_chars["delivery_street1"]=array("-",".","'"," ",",","/");
        $accepted_chars["delivery_street2"]=array("-",".","'"," ",",","/");$accepted_chars["delivery_city"]=array("-",".","'"," ");
        $accepted_chars["delivery_postal_code"]=array("-"," ");
        
        $success=1;
        while (list($key,$value) = each($_POST))
        {
            if ($key=="addresses_submitted"||$key=="submit"||$key=="password1"||$key=="password2"||$key=="delivery_phone"||$key=="voucher_ID")
            {
                
            }
            else
            {
                if ($_POST[$key]=="")
                {
                    $errors[$key]["empty"]=1;
                    $success=0;
                    //echo $key." - EMPTY";
                }
                if ($errors[$key]["empty"]!=1)
                {
                    //skip fields that don't need field length / accpetable character checking
                    //all fields the user has no ability to accidentally put error characters in
                    if ($key=="addresses_submitted"||$key=="delivery_copy_checkbox"||$key=="submit"||$key=="billing_country_code"||$key=="terms_agreed"||$key=="delivery_phone"||$key=="voucher_ID"||$key=="address_submit_x"||$key=="address_submit_y"||$key=="address_submit"||$key=="billing_state")
                    {
                        
                    }
                    else
                    {
                        //check the field lengths
                        if (strlen($_POST[$key])>$field_lengths[$key]) 
                        {                 	
                            $errors[$key]["field_length"]=$field_lengths[$key];
                            $success=0;  
                            //echo $key." - LENGTH";  
                        }
                        if ($key=="billing_first_name"||$key=="billing_last_name"||$key=="billing_city"||$key=="delivery_first_name"||$key=="delivery_last_name"||$key=="delivery_city")
                        {
                            //first set are fields where numbers are not allowed
                            if (ctype_alpha(str_replace($accepted_chars[$key],"",stripslashes($_POST[$key])))==0)
                            {
                                $errors[$key]["char"]=$accepted_chars[$key];
                                $success=0;
                            //echo $key." - ALPHA";  
                            }    
                        }
                        else
                        {
                            //second set are fields where numbers are allowed
                            if (ctype_alnum(str_replace($accepted_chars[$key],"",stripslashes($_POST[$key])))==0)
                            {
                                $errors[$key]["char"]=$accepted_chars[$key];
                                $success=0;
                            //echo $key." - ALPHANUM";  
                            }     
                        }         	
                    }
                }
            }
        }
        if (validate_email_format($_POST["primary_email"])||$_POST["primary_email"]=="")
        {
            $validate_email=1;
        }
        else
        {
            $validate_email=0;
            $success=0;
        }
        if ($_POST["terms_agreed"]!="on")
        {
            $errors["terms"]=1;
            $success=0;
        }
        if ($success==1)
        {
            //record order
            $order_id=create_order($_POST);
            initialise_sagepayment($_POST,$order_id,$host,$sage_pay_vendor_name,$site_production_status,$sage_pay_connect_string);
        }
        else
        {
            $address=$_POST;
        }
    }
    else
    {
        $address=$last_order;
    }
?>