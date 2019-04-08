<?php
    echo "<span class='file-structure-header-dev-level2'>2:address.php</span>";
    if (!isset($_SESSION["basket_ID"]))
    {
        echo "No products on order";
    }
    else
    {
        //address details form
        echo "<div id='address-form'>";
        //print_r($errors);
        if (isset($errors))
        {
            echo "<span id='address-master-error-message'>Please check your form for errors</span>";
        }
        if (isset($_SESSION["bagged_product_message"]))
        {
            echo "<a href='/basket'><span id='address-already-bagged-error'>Thank you for signing in. You have some items saved to your shopping cart from a previous session which we have added to your current checkout. Please <span id='address-click-here'>click here</span> to check that you're happy with everything before continuing to payment.</span></a>";
            unset($_SESSION["bagged_product_message"]);
        }
        else
        {
            echo     "<form method='post' id='address_form' name='address_form' action=''>";
            echo        "<div id='address-fields'>";
            echo            "<input type='hidden' name='addresses_submitted' value='1'/>";
            //contact information
            echo            "<span class='address-section-header'><h4>Contact Information:</h4></span>";
            if ($_SESSION["user"]["userType"]!="customer")
            {
                order_address_field("primary_email","Email *",$address["primary_email"],$errors,"email");
            }
            else
            {
                order_address_field("primary_email","Email *",$_SESSION["user"]["email"],$errors,"email");
            }
            order_address_field("delivery_phone","Phone Number",$address["delivery_phone"],$errors);
            //delivery address
            echo            "<span class='address-section-header'><h4>Delivery Address:</h4></span>";
            order_address_field("delivery_first_name","First Name *",$address["delivery_first_name"],$errors);
            order_address_field("delivery_last_name","Last Name *",$address["delivery_last_name"],$errors);
            order_address_field("delivery_street1","House Name/No *",$address["delivery_street1"],$errors);
            order_address_field("delivery_street2","Street *",$address["delivery_street2"],$errors);
            order_address_field("delivery_city","City *",$address["delivery_city"],$errors);
            order_address_field("delivery_postal_code","Postal Code *",$address["delivery_postal_code"],$errors);
            echo        "<span class='address_field_header' id='delivery_country_code_header'><span class='address_field_label'>Country: Delivery to UK only</span></span>";
            //billing address
            echo            "<span class='address-section-header'><h4>Billing Address:</h4></span>";
            echo            "<span id='delivery-copy-checkbox'><input type='checkbox' name='delivery_copy_checkbox' onclick='populate_address()'/>Same as delivery?</span>";
            order_address_field("billing_first_name","First Name *",$address["billing_first_name"],$errors);
            order_address_field("billing_last_name","Last Name *",$address["billing_last_name"],$errors);
            order_address_field("billing_street1","House Name/No *",$address["billing_street1"],$errors);
            order_address_field("billing_street2","Street *",$address["billing_street2"],$errors);
            order_address_field("billing_city","City *",$address["billing_city"],$errors);
            order_address_field("billing_postal_code","Postal Code *",$address["billing_postal_code"],$errors);
            $countries=mysql_query("select * from BillingCountryCodes order by country_id");
            echo        "<span class='address_field_header' id='billing_country_code_header'><span class='address_field_label'>Country*</span><span class='address_field_message' id='billing_country_code_message'></span></span>";
            echo            "<span class='address_field_input' id='billing_country_code-input'>";
            echo                "<select name='billing_country_code' id='billing_country_code' onchange='billing_state()'>";
            while ($country=mysql_fetch_array($countries))
            {
                echo "<option";
                if($country["country_code"]==$address["billing_country_code"])
                {
                    echo " selected='selected' ";
                }
                echo " name='".$country["country_code"]."' value='".$country["country_code"]."'>".$country["country_name"]."</option>";
            }
            echo                "</select>";
            echo            "</span>";
            if (strlen($address["billing_state"])>0&&$address["billing_country_code"]=="US")
            {
                echo "<span id='billing_state_marker'>";
                echo    get_billing_states($address["billing_state"]);
                echo "</span>";              
            }
            else
            {
                echo "<span id='billing_state_marker'>";
                echo "</span>";
            }
            echo            "<span id='billing_state_marker'>";
            echo            "</span>";
            if ($errors["terms"]==1)
            {
                echo "<span class='address_field_header_error'>Please agree to our terms</span>";
            }
            echo            "<span class='address_field_input' id='address-tick-box'>";
            if ($_POST["terms_agreed"]=="on")
            {
                echo            "<input type='checkbox' name='terms_agreed' checked='checked'/>";
            }
            else
            {
                echo            "<input type='checkbox' name='terms_agreed'/>";
            }
            echo            "<span id='terms-text'>Please click here to confirm you have read our <a href='/terms' target='_blank'>terms and conditions</a></span>";
            echo            "</span>";
            echo            "<span class='address_field_input' id='address-go-to-payment-button'><input id='address_submit' class='submit_button button' type='submit' name='submit' value='go to payment'/></span>";
            echo        "</div>";
            echo    "<input type='hidden' name='voucher_ID' value='".$_SESSION["voucher_ID"]."'/>";
            echo "</form>";
            $totals=get_basket_total(array("v_ID"=>$_SESSION["voucher_ID"]));
            echo "<div id='summary-panel'>";
            echo        "<h4>Total</h4>";
            echo        "<span id='order-total-display'>".format_price($totals["order"])."</span>";
            echo "</div>";
        }
    }
?>