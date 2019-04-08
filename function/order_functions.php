<?php
    function requestPost($url, $data)
    {
        // Set a one-minute timeout for this script
        set_time_limit(60);

        // Initialise output variable
        $output = array();

        // Open the cURL session
        $curlSession = curl_init();

        // Set the URL
        curl_setopt ($curlSession, CURLOPT_URL, $url);
        // No headers, please
        curl_setopt ($curlSession, CURLOPT_HEADER, 0);
        // It's a POST request
        curl_setopt ($curlSession, CURLOPT_POST, 1);
        // Set the fields for the POST
        curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $data);
        // Return it direct, don't print it out
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1); 
        // This connection will timeout in 30 seconds
        curl_setopt($curlSession, CURLOPT_TIMEOUT,30); 
        //The next two lines must be present for the kit to work with newer version of cURL
        //You should remove them if you have any problems in earlier versions of cURL
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
    
        //Send the request and store the result in an array
        
        $rawresponse = curl_exec($curlSession);
        //Store the raw response for later as it's useful to see for integration and understanding 
        $_SESSION["rawresponse"]=$rawresponse;
        //Split response into name=value pairs
        $response = split(chr(10), $rawresponse);
        // Check that a connection was made
        if (curl_error($curlSession))
        {
            // If it wasn't...
            $output['Status'] = "FAIL";
            $output['StatusDetail'] = curl_error($curlSession);
        }

        // Close the cURL session
        curl_close ($curlSession);

        // Tokenise the response
        for ($i=0; $i<count($response); $i++)
        {
            // Find position of first "=" character
            $splitAt = strpos($response[$i], "=");
            // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
            $output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
        } // END for ($i=0; $i<count($response); $i++)

        // Return the output
        return $output;
    } // END function requestPost()
    
    function order_address_field($identifier,$label,$value,$error,$field_type="")
    {
        $value=stripslashes(stripslashes($value));
        if (isset($error[$identifier]))
        {
            if (isset($error[$identifier]["char"]))
            {
                //set char message
                if ($identifier=="billing_first_name"||$identifier=="billing_last_name"||$identifier=="billing_city"||$identifier=="delivery_first_name"||$identifier=="delivery_last_name"||$identifier=="delivery_city")
                {
                    $char_message="<span class='allowed-char'>a-z</span>, <span class='allowed-char'>A-Z</span>";
                }
                else
                {
                    $char_message="<span class='allowed-char'>a-z</span>, <span class='allowed-char'>A-Z</span>, <span class='allowed-char'>0-9</span>";
                }
                //add extra characters to char_message
                foreach ($error[$identifier]["char"] as $char)
                {
                    if ($char==" ") {$char="space";}
                    $char_message=$char_message.", <span class='allowed-char'>".$char."</span>";
                }
                if (isset($error[$identifier]["field_length"]))
                {
                    echo "<span class='address_field_header_error' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'> is too long - max ".$error[$identifier]["field_length"]." - only [".$char_message."]</span></span>";
                    echo "<span class='address_field_input_error' id='".$identifier."_input'>";
                }
                else
                {
                    echo "<span class='address_field_header_error' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'> only [".$char_message."]</span></span>";
                    echo "<span class='address_field_input_error' id='".$identifier."_input'>";
                }
            }
            else
            {
                if (isset($error[$identifier]["field_length"]))
                {
                    echo "<span class='address_field_header_error' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'> is too long - max ".$error[$identifier]["field_length"]." characters</span></span>";
                    echo "<span class='address_field_input_error' id='".$identifier."_input'>";
                }
                else
                {
                    echo "<span class='address_field_header_error' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'> must be filled</span></span>";
                    echo "<span class='address_field_input_error' id='".$identifier."_input'>";
                }
            }
        }
        else
        {
            if (($error["email_taken"]==1||$error["invalid_email"])&&$identifier=="primary_email")
            {
                echo "<span class='address_field_header_error' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'> already taken / invalid</span></span>";
                echo "<span class='address_field_input_error' id='".$identifier."_input'>";
            }
            else
            {
                //no errors, or used to focus js feedback
                echo "<span class='address_field_header' id='".$identifier."_header'><span class='address_field_label'>".$label."</span><span class='address_field_message' id='".$identifier."_message'></span></span>";
                echo "<span class='address_field_input' id='".$identifier."_input'>";
            }
        }
        if ($field_type=="email")
        {
            //echo "<input type='text' name='".$identifier."' id='".$identifier."' value='".$value."' onkeyup='check_email_input()'/>";
            echo "<input class='text_field' type='text' name='".$identifier."' id='".$identifier."' value='".$value."'/>";
        }
        else
        {
            //quotation marks reversed for output of names with apostrophies
            echo '<input class="text_field" type="text" name="'.$identifier.'" id="'.$identifier.'" value="'.$value.'"/>';
        }
        echo "</span>";
    }
    function get_billing_states($current_state)
    {
        $billing_states=site_query("select * from BillingStateCode order by stateName","billing state get",0);
        $html="<span class='address_field_header' id='billing_state_header'><span class='address_field_label'>State*</span><span class='address_field_message' id='billing_state_message'></span></span>";
        $html=$html."<span class='address-input' id='billing_state-input'>";
        $html=$html."<select name='billing_state' id='billing_state'>";
        while ($billing_state=mysql_fetch_array($billing_states))
        {
            $html=$html."<option";
            if($billing_state["stateCode"]==$current_state)
            {
                $html=$html." selected='selected' ";
            }
            $html=$html." name='".$billing_state["stateCode"]."' value='".$billing_state["stateCode"]."'>".$billing_state["stateName"]."</option>";
        }
        $html=$html."</select>";
        $html=$html."</span>";
        return $html;
    }
    /*
     gets a set of orders from the db - will retrieve all if no restrictings defined
     $basket_ID = focuses on a particular customer
     $order_status = restricts based on status
    */
    function get_orders($basket_ID,$order_status="")
    {
        $dev=0;
        $clause_counter=1;
        $orders_string="select * from UserOrder ";
        if ($basket_ID!="")
        {
            $orders_string=$orders_string."where basketID='".$basket_ID."' ";
            $clause_counter=$clause_counter+1;
        }
        if ($order_status!="")
        {
            if ($clause_counter==2) {$orders_string=$orders_string."and ";} else {$orders_string=$orders_string."where ";}
            $orders_string=$orders_string."order_status='".$order_status."' ";
            $clause_counter=$clause_counter+1;
        }
        $orders_string=$orders_string."order by orderDate";
        $orders_query=site_query($orders_string,"get_orders()",$dev);   
        return $orders_query;
    }
    /*
     retrieves the details of a particular order
     $order_ID = the ID of the order to retrieve
    */
    function get_order($order_ID)
    {
        $dev=0;
        $order_string="select * from UserOrder where orderID=".$order_ID;
        $order_query=site_query($order_string,"get_order()",$dev);   
        return mysql_fetch_array($order_query);
    }
    function initialise_sagepayment($address,$order_id,$host,$sage_pay_vendor_name,$site_production_status,$sage_pay_connect_string)
    {
        if ($address["address_same"]=="on")
        {
            $address["billing_first_name"]=mysql_real_escape_string($address["delivery_first_name"]);
            $address["billing_last_name"]=mysql_real_escape_string($address["delivery_last_name"]);
            $address["billing_street1"]=mysql_real_escape_string($address["delivery_street1"]);
            $address["billing_street2"]=mysql_real_escape_string($address["delivery_street2"]);
            $address["billing_city"]=mysql_real_escape_string($address["delivery_city"]);
            $address["billing_postal_code"]=mysql_real_escape_string($address["delivery_postal_code"]);
        }
        $totals=get_basket_total(array("v_ID"=>$address["voucher_ID"],"o_ID"=>$order_id));
        if ($totals["order"]<=0) //a voucher code has been used that has reduced the order cost to £0
        {
            $unique_order_key=place_completed_order($order_id,$site_production_status);
            header("location:http://".$host."/order-complete/".$unique_order_key);
        }
        else
        {
            $order_details="";
            $order_details=$order_details."VPSProtocol=2.23&TxType=PAYMENT&Vendor=".$sage_pay_vendor_name."&VendorTxCode=".$order_id."&Amount=".number_format($totals["order"],2,".","");
            $order_details=$order_details."&Currency=GBP&Description=".urlencode("Basket of products")."&NotificationURL=".urlencode("http://".$host."/payment_processed.php");
            $order_details=$order_details."&BillingSurname=".urlencode($address["billing_last_name"])."&BillingFirstnames=".urlencode($address["billing_first_name"]);
            $order_details=$order_details."&BillingAddress1=".urlencode($address["billing_street1"])."&BillingAddress2=".urlencode($address["billing_street2"]);
            $order_details=$order_details."&BillingCity=".urlencode($address["billing_city"])."&BillingPostCode=".urlencode($address["billing_postal_code"]);
            //we have an American card, the state registered is required
            if (strlen($billing_state)>0&&$billing_country_code=="US")
            {
                $order_details=$order_details."&BillingState=".urlencode($address["billing_state"]);
            }
            $order_details=$order_details."&BillingCountry=".urlencode($address["billing_country_code"])."&DeliverySurname=".urlencode($address["delivery_last_name"]);
            $order_details=$order_details."&DeliveryFirstnames=".urlencode($address["delivery_first_name"])."&DeliveryAddress1=".urlencode($address["delivery_street1"]);
            $order_details=$order_details."&DeliveryAddress2=".urlencode($address["delivery_street2"])."&DeliveryCity=".urlencode($address["delivery_city"]);
            $order_details=$order_details."&DeliveryPostCode=".urlencode($address["delivery_postal_code"])."&DeliveryPhone=".urlencode($address["delivery_phone"]);
            $order_details=$order_details."&DeliveryCountry=GB&CustomerEMail=".urlencode($address["primary_email"]);
            /*dev_dump($order_details,"Order String",1);*/
            $result=requestPost($sage_pay_connect_string,$order_details);
            if ($result["Status"]=="OK")
            {
                site_query("update UserOrder set VPSTxId='".$result["VPSTxId"]."', SecurityKey='".$result["SecurityKey"]."' where orderID=".$order_id,"order_address.php - save sage pay data",$dev);
                header("Location:".$result["NextURL"]);
            }
            else
            {
                echo "There has been a technical issue with your payment. Please try again later. Sorry for the incovenience.";
                dev_dump($result,"Sage Pay Bounce",1);
            }
        }
    }
    function create_order($address)
    {
        if ($address["address_same"]=="on")
        {
            $address["billing_first_name"]=mysql_real_escape_string($address["delivery_first_name"]);
            $address["billing_last_name"]=mysql_real_escape_string($address["delivery_last_name"]);
            $address["billing_street1"]=mysql_real_escape_string($address["delivery_street1"]);
            $address["billing_street2"]=mysql_real_escape_string($address["delivery_street2"]);
            $address["billing_city"]=mysql_real_escape_string($address["delivery_city"]);
            $address["billing_postal_code"]=mysql_real_escape_string($address["delivery_postal_code"]);
        }
        $totals=get_basket_total(array("v_ID"=>$address["voucher_ID"]));
        $order_query="INSERT INTO UserOrder (basketID, orderStatus, orderTotal, productsTotal, postageTotal, primary_email, delivery_first_name, delivery_last_name, delivery_phone, "
                  ."delivery_street1, delivery_street2, delivery_city, delivery_postal_code, billing_first_name, billing_last_name, billing_street1, "
                  ."billing_street2, billing_city, billing_postal_code, billing_state,billing_country_code, host, voucherID) VALUES "
                  ."('".$_SESSION["basket_ID"]."', 'in_checkout', ".$totals["order"].", ".($totals["order"]-$totals["postage"]).", ".$totals["postage"].", '".$address["primary_email"]."', '".$address["delivery_first_name"]."', '".$address["delivery_last_name"]."',
                  '".$address["delivery_phone"]."', '".$address["delivery_street1"]."', '".$address["delivery_street2"]."', '".$address["delivery_city"]."',
                  '".$address["delivery_postal_code"]."', '".$address["billing_first_name"]."', '".$address["billing_last_name"]."', '".$address["billing_street1"]."', '".$address["billing_street2"]."',
                  '".$address["billing_city"]."', '".$address["billing_postal_code"]."', '".$address["billing_state"]."', '".$address["billing_country_code"]."', '".$_SERVER["REMOTE_ADDR"]."','".$address["voucher_ID"]."')";
        site_query($order_query,"order_address.php - insert order into database",$dev);
        $order_id=mysql_insert_id();
        //update basket with order id so the order products can be found
        $basket=get_basket();
        while ($basket_product=mysql_fetch_array($basket))
        {
            site_query("update Basket set orderID=".$order_id." where basketItemID=".$basket_product["basketItemID"],"order_address.php - update Basket with order id",$dev);
        }
        unset($_SESSION["voucher_ID"]);
        return $order_id;
    }
    function place_completed_order($order_ID,$strConnectTo)
    {
        $basket=get_basket($order_ID);
        //iterate over basket sending emails to suppliers
        site_query("update UserOrder set orderStatus='payment_received' where orderID=".$order_ID,"place_completed_order() - mark payment received",$dev);
        $customer_order=get_order($order_ID);
        $order_link_key=md5($customer_order["orderID"].$customer_order["delivery_street1"].$customer_order["billing_country_code"].$customer_order["orderDate"]);
        site_query("update UserOrder set orderLinkKey='".$order_link_key."' where orderID=".$customer_order["orderID"],"order_functions.php - update orderLinkKey",$dev);
        if ($customer_order["voucherID"]!="")
        {
            dev_dump("valid voucher",$customer_order["voucherID"],$dev);
            $voucher_query=site_query("select * from Voucher where voucherID='".$customer_order["voucherID"]."'","place_completed_order() - voucher code",$dev);
            $voucher=mysql_fetch_array($voucher_query);
            dev_dump("voucher details",$voucher,$dev);
            $voucher_spendable_query=site_query("select * from VoucherType where voucherTypeID=".$voucher["voucherTypeID"],"place_completed_order() - voucher type",$dev);
            $voucher_ID_spendable=mysql_fetch_array($voucher_spendable_query);
            if ($voucher["singleShot"]==1)
            {
                site_query("update Voucher set spent=1 where voucherID='".$customer_order["voucherID"]."'","place_completed_order() - voucher code used",$dev);
            }
        }
        return $order_link_key;
    }
?>
