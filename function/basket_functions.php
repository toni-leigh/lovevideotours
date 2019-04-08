<?php
    /*
     sets the bag id
    */
    function set_basket_ID()
    {
        if ($_SESSION["basket_ID"])
        {      
        }
        else
        {
            //build a random unique key for an anonymous bag
            if ($_SESSION["user"])
            {
                $_SESSION["basket_ID"]=$_SESSION["user"]["userID"];
            }
            else
            {
                if ($_COOKIE["basket_ID"])
                {
                    $_SESSION["basket_ID"]=$_COOKIE["basket_ID"];
                }
                else
                {
                    $time=time();
                    $random_string=random_string(25);
                    $_SESSION["basket_ID"]=md5($time.$random_string);
                }
            }
        }
    }
    /*
     saves a product into the bag
     $in["i_ID"] = the bagged item ID
     $in["v_ID"] = the bagged variation ID
     $in["q"] = the number bagged
     $in["u_ID"] = the user who created the bagged item (!NOT the user who does the bagging)
    */
    function bag_product($in)
    {
        $dev=0;
        $fail_quantity=0;
        if (!$_SESSION["basket_ID"]) {set_basket_ID();}
        //check the quantity, then add or update bag
        $bagged=site_query("select * from Basket where basketID='".$_SESSION["basket_ID"]."' and itemID=".$in["i_ID"]." and variationID=".$in["v_ID"]." and orderID=0 and removed=0","bag_product() check bagged",$dev);
        if (is_numeric($in["q"])&&$in["q"]>0)
        {
            if (mysql_num_rows($bagged)>0)
            {
                $bs="update Basket set quantity=quantity+".$in["q"]." where basketID='".$_SESSION["basket_ID"]."' and itemID=".$in["i_ID"]." and variationID=".$in["v_ID"]." and orderID=0";
                site_query($bs,"bag_product() update",$dev); 
            }
            else
            {
                $bs="insert into Basket (basketID,itemID,supplierID,variationID,quantity) values ('".$_SESSION["basket_ID"]."',".$in["i_ID"].",".$in["u_ID"].",".$in["v_ID"].",".$in["q"].")";
                site_query($bs,"bag_product() new",$dev); 
            }
        }
        else
        {
            $fail_quantity=1;
        }
        return $fail_quantity;
    }
    /*
     builds a basket button
     includes the variation drop down if more than one variation is present
    */
    function basket_button($in)
    {
        $dev=0;
        if ($in["err"])
            echo "Bad Quantity";
            
        // build variation dropdown
        $bs=variation_selector($in);
        
        // get the quantity value, will be set on form resubmit else 1
        if (isset($_POST["quantity"])) {$quantity_value=$_POST["quantity"];} else {$quantity_value=1;}
        
        // get the main variation
        $main_variation=get_main_variation($in["product"]["itemID"]);
        $vstring=get_variation_text($main_variation["variationID"]);
        
        // build basket button
        $bb="";
        $bb.="<div id='price'>";
        $bb.=format_price($main_variation["price"]);
        $bb.="</div>";
        $bb.="<div id='main_var'>";
        $bb.=$vstring;
        $bb.="</div>";
        if (isset($in["list_button"]))
            $bb.="<span id='basket_button_panel".$in["product"]["itemID"]."' class='list_basket_button'>";
        else
            $bb.="<span id='basket_button_panel".$in["product"]["itemID"]."' class='basket_button'>";
        $bb.="<form method='post' action=''>";
        $bb.="<input type='hidden' name='basket_submit' value='".$in["product"]["itemID"]."'/>";
        $bb.="<input type='hidden' name='item_ID' value='".$in["product"]["itemID"]."'/>";
        $bb.="<input type='hidden' name='user_ID' value='".$in["product"]["userID"]."'/>";
        $bb.="quantity: <input class='text_field' type='text' name='quantity".$in["product"]["itemID"]."' value='".$quantity_value."' id='quantity' class='form-field'/>";
        $bb.=$bs;
        $bb.="<input id='basket_submit".$in["product"]["itemID"]."' class='checkout_path_button pbasketbutton left' type='submit' name='submit' value='buy me !'/>";
        $bb.="</form>";
        $bb.="</span>";
        $bb.=open_script();
        $bb.="new_html='quantity: <input class=\"text_field\" type=\"text\" name=\"quantity".$in["product"]["itemID"]."\" value=\"".$quantity_value."\" id=\"quantity".$in["product"]["itemID"]."\" class=\"form_field\"/>';";
        $bb.="new_html+='".$bs."';";
        $bb.="new_html+='<span class=\"checkout_path_button pbasketbutton left\" onclick=\"updateBag(".$in["product"]["itemID"].",".$in["product"]["userID"].")\">buy me !</span>';";
        $bb.="document.getElementById('basket_button_panel".$in["product"]["itemID"]."').innerHTML=new_html;";
        $bb.=close_script();
        return $bb;
    }
    function variation_selector($in)
    {
        $bs="";
        $variations=get_product_variations(array("i_ID"=>$in["product"]["itemID"],"in_stock"=>0));
        if (mysql_num_rows($variations)>1&&$in["simple"]==0)
        {
            $bs.="<select name=\"item_variation".$in["product"]["itemID"]."\" id=\"item_variation".$in["product"]["itemID"]."\" class=\"variation_select\">";
            while($variation=mysql_fetch_array($variations))
            {
                $vstring=get_variation_text($variation["variationID"]);
                $bs.="<option value=\"".$variation["variationID"]."\">".$vstring." ".format_price($variation["price"])."</option>";
            }
            $bs.="</select>";        
        }
        else
        {
            //get the first variation, which will be main if main is marked
            $variation=mysql_fetch_array($variations);
            $bs.="<input type=\"hidden\" name=\"item_variation".$in["product"]["itemID"]."\" id=\"item_variation".$in["product"]["itemID"]."\" value=\"".$variation["variationID"]."\"/>";
        }
        return $bs;
    }
    /*
     get customer bag details - calculate the bag total including voucher if applied
     $in["v_ID"] = the voucher ID
     $in["o_ID"] = the order ID
     $in["postage_type"] = the type of delivery for the calculation
    */
    function get_basket_total($in=array())
    {
        if (isset($_SESSION["basket_ID"]))
        {
            $totals["order"]=0;
            $totals["postage"]=0;
            $product_sub_total=get_basket_product_cost(array("o_ID"=>$in["o_ID"]));
            $basket=get_basket(array("o_ID"=>$in["o_ID"]));
            $basket_weight=get_bagged_weight($basket);
            $postage_cost=get_weight_bracket($basket_weight,1);
            $totals["order"]=$totals["order"]+$product_sub_total+$postage_cost[$in["postage_type"]];
            $totals["postage"]=$totals["postage"]+$postage_cost[$in["postage_type"]];
            //then apply voucher
            if ($in["v_ID"]!="")
                $totals["order"]=apply_voucher($totals["order"],$totals["order"]-$totals["postage"],$totals["postage"],$in["v_ID"]);
            return $totals;
        }
        else
        {
            return 0;
        }
    }
    /*
     resets a bag on order fail
     needs to leave the bagged products as on order (failed) while copying them over to the bag as not on order ready for the customer to try again
     also needs to copy the corresponding supplier bag records
    */
    function reset_basket_on_fail($in)
    {
        $b=site_query("select * from Basket where orderID=".$in["o_ID"],"reset_basket_on_fail() - get order basket");
        while ($bp=mysql_fetch_array($b))
        {
            //copy record
            $bp=get_basket_product($bp["basketItemID"]);
            $bq="insert into Basket (basketID,itemID,variationID,quantity,baggedTime,removed) values";
            $bq=$bq." ('".$bp["basketID"]."',".$bp["itemID"].",".$bp["variationID"].",".$bp["quantity"].",'".$bp["baggedTime"]."',0)";
            site_query($bq,"copy bagged product in order failed",$dev);
        }
    }
    /*
     gets the number of products in bag
    */
    function count_basket_products()
    {
        $count=0;
        $dev=0;
        $basket=get_basket();
        while ($basket_product=mysql_fetch_array($basket))
            $count=$count+$basket_product["quantity"];
        return $count;
    }
    /*gets bagged products
     ** $supplier_ID = the supplier ID used to restrict the contents to just the bagged products for a particular supplier - the default
     ** returns all of a customers bagged products
     ** */
    function get_basket($in=array())
    {
        $dev=0;
        if (is_numeric($in))
        {
            $temp=$in;
            unset($in);
            $in["o_ID"]=$temp;
        }
        else
        {
            if (!is_numeric($in["o_ID"])) $in["o_ID"]=0;
        }
        $basket=site_query("select * from Basket, Item, Product, ProductVariation, User, Supplier where basketID='".$_SESSION["basket_ID"]."' and Basket.orderID=".$in["o_ID"]." and Basket.removed=0 and Item.itemID=Product.itemID and Basket.itemID=Product.itemID and Basket.variationID=ProductVariation.variationID and Basket.supplierID=User.userID and Basket.supplierID=Supplier.userID order by User.displayName","get_basket()",$dev);
        return $basket;
    }
    /*
     gets the total cost of the bag without postage - is used in the header to create the shopping bag summary
     also for working out totals
    */
    function get_basket_product_cost($in)
    {
        $tot=0.0;
        $b=get_basket(array("o_ID"=>$in["o_ID"]));
        while ($bp=mysql_fetch_array($b))
            $tot=$tot+$bp["price"]*$bp["quantity"];
        return $tot;
    }
    /*
     gets the weight of a set of bagged products - this is then used to calculate the retrieve the postage costs for a supplier bag
    */
    function get_bagged_weight($b)
    {
        $tot=0;
        while ($bp=mysql_fetch_array($b))
            $tot=$tot+(($bp["quantity"]*$bp["grossPackageValue"]));
        return $tot;
    }
    /*
     gets the details of postage for a particular weight - then used to give the correctly priced postage options for a supplier bag
     also used for item count
    */
    function get_weight_bracket($weight,$user_ID)
    {
        $dev=0;
        $weight_brackets=site_query("select * from PostageCharges where userID=".$user_ID." order by minValue","get_weight_bracket()",$dev);
        while ($weight_bracket=mysql_fetch_array($weight_brackets))
        {
            //this will catch the parcel in the lower of the two brackets for those on the cusp
            //ie 1-5 items, 5 will evaluate as being in 1-5, regardless of whether the next bracket is 5-10 or 6-10
            if ($weight>=$weight_bracket["minValue"]&&$weight<=$weight_bracket["maxValue"])
            {
                return $weight_bracket;
            }
        }
    }
    /*
     gets the total price of a bagged product taking into account the quantity bagged
    */
    function get_bag_item_total($basket_product)
    {
        return $basket_product["quantity"]*$basket_product["price"];
    }
    function basket_product($basket_product)
    {
        $vstring=get_variation_text($basket_product["variationID"]);
        $bp="";
        $bp.="<div class='basket_product left'>";
        $bp.="<span class='basket_product_name left'>".$basket_product["itemName"]." [".$vstring."]</span>";
        $bp.="<span class='basket_product_price left'>".format_price($basket_product["price"]*$basket_product["quantity"])."</span><span class='basket_product_price_each left'>[".format_price($basket_product["price"])." each]</span>";
        $bp.="<input class='basket_remove right' type='checkbox' name='".$basket_product["basketItemID"]."remove"."'/>";
        $bp.="<input class='text_field right' type='text' name='".$basket_product["basketItemID"]."' value='".$basket_product["quantity"]."'/>";
        $bp.="</div>";
        return $bp;
    }
    /*
     builds the html output of the cart - for the header
    */
    function build_html_basket()
    {
        $html="";
        if ($_SESSION["basket_ID"])
        {
            $html="<a href='/basket'>";
            $html.="<div id='page_basket_panel' class='checkout_path_button'>basket:";
            $product_count=count_basket_products();
            if ($product_count==1)
                $html.=$product_count." item";
            else
                $html.=$product_count." items";
            $totals=get_basket_total();
            $html.="&nbsp;".format_price($totals["order"]);
            $html.="</div>";
            $html.="</a>";
            $html.=build_straight_to_payment_button();
        }
        else
        {
            $html="<div id='page_basket_panel' class='checkout_path_button'>basket";
            $html.="</div>";
        }
        return $html;
    }
    function build_straight_to_payment_button($type="")
    {
        $dev=0;
        //only appears if straight to payment is an option
        //user must be signed in & full order address must be present (i.e. they must placed at least one order with the site)
        //retrieve last order for addresses
        $last_order_query=site_query("select * from UserOrder where basketID='".$_SESSION["basket_ID"]."' order by orderID desc limit 1","order_address.php - last order query",$dev);
        $totals=get_basket_total();
        //order total must be above 0, no voucher possible
        if (mysql_num_rows($last_order_query)>0&&$totals["order"]>0)
        {
            if ($type=="form")
            {
                return "<input id='straight_to_payment_submit' class='checkout_path_button left' type='submit' name='submit' value='straight to payment'/>";
            }
            else
            {
                return "<a href='/fast-track'>Straight to Payment</a>";
            }
        }
        else
        {
            return "";
        }
    }
?>