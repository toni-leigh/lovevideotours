<?php
    include 'function/global_functions.php';
    include 'function/initialise.php';
    include 'function/basket_functions.php';
    include "function/dev_functions.php";
    include 'function/order_functions.php';
    include 'function/product_functions.php';
    include 'function/user_functions.php';
    include 'function/voucher_functions.php';
    // Filters unwanted characters out of an input string.  Useful for tidying up FORM field inputs
    function cleanInput($strRawText,$strType)
    {
    
        if ($strType=="Number") {
            $strClean="0123456789.";
            $bolHighOrder=false;
        }
        else if ($strType=="VendorTxCode") {
            $strClean="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
            $bolHighOrder=false;
        }
        else {
            $strClean=" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,'/{}@():?-_&£$=%~<>*+\"";
            $bolHighOrder=true;
        }
        
        $strCleanedText="";
        $iCharPos = 0;
                
        do
        {
        // Only include valid characters
        $chrThisChar=substr($strRawText,$iCharPos,1);
                
        if (strspn($chrThisChar,$strClean,0,strlen($strClean))>0) { 
                $strCleanedText=$strCleanedText . $chrThisChar;
        }
        else if ($bolHighOrder==true) {
                // Fix to allow accented characters and most high order bit chars which are harmless 
                if (bin2hex($chrThisChar)>=191) {
                    $strCleanedText=$strCleanedText . $chrThisChar;
                }
            }
                
        $iCharPos=$iCharPos+1;
        }
        while ($iCharPos<strlen($strRawText));
                
        $cleanInput = ltrim($strCleanedText);
        return $cleanInput;
    
    }
    //print_r($_POST);
    function responder($status,$host,$redirect,$message,$reason_code,$order_id)
    {
        if ($reason_code>0)
        {
            $reason="/".$reason_code;
        }
        else
        {
            $reason="";
        }
        $eoln = chr(13) . chr(10);
        ob_flush();
        header("Content-type: text/plain");
        echo "Status=".$status.$eoln;
        echo "RedirectURL=http://".$host.$redirect.$reason.$eoln;  
        echo "StatusDetail=".$message.$eoln;
        exit();
    }
    if ($_POST["Status"]=="OK")
    {
        $strStatus=cleaninput($_REQUEST["Status"],"Text");
        $strVendorTxCode=cleaninput($_REQUEST["VendorTxCode"],"VendorTxCode");
        $strVPSTxId=cleaninput($_REQUEST["VPSTxId"],"Text");
        //set order status = 'payment successful'
        $result=site_query("select * from UserOrder where orderID=".mysql_real_escape_string($strVendorTxCode),"get transaction from the db in payment_processed.php",$dev);
        if (mysql_num_rows($result)!=1)
        {
            responder("INVALID",$host,"/order-complete","Transaction not found in Database",1,0);
            //return ERROR
        }
        else
        {
            $extract=mysql_fetch_array($result);
            $strSecurityKey=$extract["SecurityKey"];
            $order_id=$extract["orderID"];
            /** We've found the order in the database, so now we can validate the message **
            ** First blank out our result variables **/
            $strStatusDetail="";
            $strTxAuthNo="";
            $strAVSCV2="";
            $strAddressResult="";
            $strPostCodeResult="";
            $strCV2Result="";
            $strGiftAid="";
            $str3DSecureStatus="";
            $strCAVV="";
            $strAddressStatus="";
            $strPayerStatus="";
            $strCardType="";
            $strLast4Digits="";
            $strMySignature="";
            $strVendorName=$sage_pay_vendor_name;
            //$strVendorName="tastydevelopmen";
            
            /** Now get the VPSSignature value from the POST, and the StatusDetail in case we need it **/
            $strVPSSignature=cleaninput($_REQUEST["VPSSignature"],"Text");
            $strStatusDetail=cleaninput($_REQUEST["StatusDetail"],"Text");
    
            /** Retrieve the other fields, from the POST if they are present **/
            if (strlen($_REQUEST["TxAuthNo"]>0)) $strTxAuthNo=cleaninput($_REQUEST["TxAuthNo"],"Number");
            $strAVSCV2=cleaninput($_REQUEST["AVSCV2"],"Text");
            $strAddressResult=cleaninput($_REQUEST["AddressResult"],"Text");
            $strPostCodeResult=cleaninput($_REQUEST["PostCodeResult"],"Text");
            $strCV2Result=cleaninput($_REQUEST["CV2Result"],"Text");
            $strGiftAid=cleaninput($_REQUEST["GiftAid"],"Number");
            $str3DSecureStatus=cleaninput($_REQUEST["3DSecureStatus"],"Text");
            $strCAVV=cleaninput($_REQUEST["CAVV"],"Text");
            $strAddressStatus=cleaninput($_REQUEST["AddressStatus"],"Text");
            $strPayerStatus=cleaninput($_REQUEST["PayerStatus"],"Text");
            $strCardType=cleaninput($_REQUEST["CardType"],"Text");
            $strLast4Digits=cleaninput($_REQUEST["Last4Digits"],"Text");
    
            /** Now we rebuilt the POST message, including our security key, and use the MD5 Hash **
            ** component that is included to create our own signature to compare with **
            ** the contents of the VPSSignature field in the POST.  Check the Sage Pay Server protocol **
            ** if you need clarification on this process **/
            $strMessage=$strVPSTxId . $strVendorTxCode . $strStatus . $strTxAuthNo . $strVendorName . $strAVSCV2 . $strSecurityKey 
                           . $strAddressResult . $strPostCodeResult . $strCV2Result . $strGiftAid . $str3DSecureStatus . $strCAVV
                           . $strAddressStatus . $strPayerStatus . $strCardType . $strLast4Digits ;
    
            $strMySignature=strtoupper(md5($strMessage));
            if ($strMySignature!==$strVPSSignature)
            {
                //add tamper to order
                $order_tampered=mysql_query("update UserOrder set orderStatus='Tamper' where UserOrderID=".$order_id);
                //reset the bag records, Basket to not on order (so the bag refills) - and supplier bag records emptied so no supplier5 orders are generated
                $basket="";
                reset_basket_on_fail(array("o_ID"=>$order_id));
                responder("INVALID",$host,"/order-complete","MD5 error - Communication possibly tampered with",2,$order_id);
            }
            else
            {
                //order paid
                $unique_order_key=place_completed_order($order_id,$site_production_status);
                responder("OK",$host,"/order-complete/".$unique_order_key,"Order verified - take payment",0,$order_id);
            }
        }
    }
    else
    {        
        //set order status = 'payment failed'
        $strVendorTxCode=cleaninput($_REQUEST["VendorTxCode"],"VendorTxCode");
        $result=mysql_query("select * from UserOrder where orderID=".mysql_real_escape_string($strVendorTxCode));
        $extract=mysql_fetch_array($result);
        $order_id=$extract["orderID"];
        $order_complete=mysql_query("update UserOrder set orderStatus='payment_failed - ".mysql_real_escape_string($_POST["Status"])."' where orderID=".$order_id);
        reset_basket_on_fail(array("o_ID"=>$order_id));
        if ($_POST["Status"]=="NOTAUTHED")
        {
            responder("OK",$host,"/order-complete","Transaction not authorised",2,$order_id);
        }
        elseif ($_POST["Status"]=="ABORT")
        {
            responder("OK",$host,"/order-complete","Transaction aborted",3,$order_id);
        }
        elseif ($_POST["Status"]=="REJECTED")
        {
            responder("OK",$host,"/order-complete","Transaction rejected",4,$order_id);
        }
        else
        {
            responder("OK",$host,"/order-complete","Error",5,$order_id);
        }
        //message
    }
    //display links to shop and bag
?>
