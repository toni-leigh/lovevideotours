<?php
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
        $ajax_message="<span class='voucher_fail_message'>coupon expired, sorry</span>";
    }
    else
    {
        if ($voucher["spent"]==1)
        {
            $ajax_message="<span class='voucher_fail_message'>this coupon has already been used</span>";
        }
        else
        {
            if (($voucher["adjustFocus"]=="total"&&$order_total<$voucher["threshold"])||($voucher["adjustFocus"]=="product"&&$product_total<$voucher["threshold"])||($voucher["adjustFocus"]=="postage"&&$postage_total<$voucher["threshold"]))
            {
                //threshold fail
                if ($voucher["adjustFocus"]=="total"){$ajax_message="<span class='voucher_success_message'>you have not spent enough on your order</span>";}
                elseif ($voucher["adjustFocus"]=="product"){$ajax_message="<span class='voucher_success_message'>you have not spent enough on your products</span>";}
                elseif ($voucher["adjustFocus"]=="postage"){$ajax_message="<span class='voucher_success_message'>you have not spent enough on your postage</span>";}                  
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
                    $ajax_order_total="<span class='voucher_success_message'>&pound;".number_format($order_total,2)."</span>";
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
                    $ajax_product_total="<span class='voucher_success_message'>products: &pound;".number_format($product_total,2)."</span>";
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
                    $ajax_postage_total="<span class='voucher_success_message'>postage: &pound;".number_format($postage_total,2)."</span>";
                }
                //only effect the order value display (we are on the order address form)
                $ajax_message="<span class='voucher_success_message'>'".get_voucher_name($voucher)."' applied</span>";  
            }
            if ($voucher["adjustFocus"]!="total")
            {
                $order_total=$postage_total+$product_total;
                $ajax_order_total="<span class='voucher_success_message'>&pound;".number_format($order_total,2)."</span>";
            }
        }
    }
?>