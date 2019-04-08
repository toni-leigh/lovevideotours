<?php
    function get_voucher_name($voucher_type)
    {
        if ($voucher_type["adjustType"]=="percentage"&&$voucher_type["adjustValue"]>99)
        {
            $voucher_type_adjust="FREE! ";
        }
        else
        {
            if ($voucher_type["adjustType"]=="pound"){$voucher_type_adjust=format_price($voucher_type["adjustValue"])." off ";}
            else {$voucher_type_adjust=$voucher_type["adjustValue"]."% off ";}
        }
        if ($voucher_type["adjustFocus"]=="total"){$voucher_type_adjust_focus="Order";}
        elseif ($voucher_type["adjustFocus"]=="product"){$voucher_type_adjust_focus="Product";}
        else {$voucher_type_adjust_focus="Postage";}
        if ($voucher_type["adjustType"]=="percentage"&&$voucher_type["adjustValue"]>99)
        {
            $voucher_name=$voucher_type_adjust.$voucher_type_adjust_focus;
        }
        else
        {
            $voucher_name=$voucher_type_adjust.$voucher_type_adjust_focus." Total";
        }
        if ($voucher_type["threshold"]>0)
        {
            $voucher_name=$voucher_name." over ".format_price($voucher_type["threshold"]);
        }
        return $voucher_name;
    }
    function get_code_details($voucher_code)
    {
        $voucher_query=site_query("select * from Voucher, VoucherType where Voucher.voucherID='".$voucher_code."' and Voucher.voucherTypeID=VoucherType.voucherTypeID","get_code_details() - voucher codes",$dev);
        if (mysql_num_rows($voucher_query)>0)
        {
            return mysql_fetch_array($voucher_query);
        }
        else
        {
            return "voucher_fail";
        }
    }
    function apply_voucher($order_total,$product_total,$postage_total,$voucher_ID)
    {
        $voucher=get_code_details($voucher_ID);
        if ($voucher!="voucher_fail")
        {
            $year=substr($voucher["expires"],0,4);
            $month=substr($voucher["expires"],5,2);
            $day=substr($voucher["expires"],8,2);
            $hour=substr($voucher["expires"],11,2);
            $minute=substr($voucher["expires"],14,2);
            $second=substr($voucher["expires"],17,2);
            $expires=mktime($hour,$minute,$second,$month,$day,$year);
            //set our values for processing
            if ($expires<time())
            {
            }
            else
            {
                if ($voucher["spent"]==1)
                {
                }
                else
                {
                    if (($voucher["adjustFocus"]=="total"&&$order_total<$voucher["threshold"])||($voucher["adjustFocus"]=="product"&&$product_total<$voucher["threshold"])||($voucher["adjustFocus"]=="postage"&&$postage_total<$voucher["threshold"]))
                    {             
                    }
                    else
                    {
                        //the voucher is valid and can be used
                        //set our three new values
                        if ($voucher["adjustFocus"]=="total")
                        {
                            if ($voucher["adjustType"]=="pound") {$order_total=$order_total-$voucher["adjustValue"];}
                            elseif ($voucher["adjustType"]=="percentage")
                            {
                                if ($voucher["adjustValue"]==100)
                                {
                                    $order_total=0;
                                }
                                else
                                {
                                    $order_total=number_format($order_total-(($order_total*$voucher["adjustValue"])/100),2);
                                }
                            }
                            if ($order_total<0) {$order_total=0;}
                            $ajax_order_total="<span class='voucher-success-message'>&pound;".number_format($order_total,2)."</span>";
                        }
                        elseif ($voucher["adjustFocus"]=="product")
                        {
                            if ($voucher["adjustType"]=="pound") {$product_total=$product_total-$voucher["adjustValue"];}
                            elseif ($voucher["adjustType"]=="percentage")
                            {
                                if ($voucher["adjustValue"]==100)
                                {
                                    $product_total=0;
                                }
                                else
                                {
                                    $product_total=number_format($product_total-(($product_total*$voucher["adjustValue"])/100),2);
                                }
                            }
                            if ($product_total<0) {$product_total=0;}
                            $ajax_product_total="<span class='voucher-success-message'>Products: &pound;".number_format($product_total,2)."</span>";
                        }
                        elseif ($voucher["adjustFocus"]=="postage")
                        {
                            if ($voucher["adjustType"]=="pound") {$postage_total=$postage_total-$voucher["adjustValue"];}
                            elseif ($voucher["adjustType"]=="percentage")
                            {
                                if ($voucher["adjustValue"]==100)
                                {
                                    $postage_total=0;
                                }
                                else
                                {
                                    $postage_total=number_format($postage_total-(($postage_total*$voucher["adjustValue"])/100),2);
                                }
                            }
                            if ($postage_total<0) {$postage_total=0;}
                            $ajax_postage_total="<span class='voucher-success-message'>Postage: &pound;".number_format($postage_total,2)."</span>";
                        }
                        //effect the actual order values - we set $_SESSION["order_total"] && $_SESSION["postage_total"] - the two values to be stored in the order record
                        if ($voucher["adjustFocus"]=="total")
                        {
                        }
                        else
                        {
                            $order_total=$product_total+$postage_total;
                        }
                    }
                }
            }
        }
        return $order_total;
    }
?>