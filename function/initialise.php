<?php
    include '../lovev/nrth.php';
    if ($_SERVER["HTTP_HOST"]=="local.lovevideotours.com") //the development version of the DB
    {
        $facebook_appid="211560658870593";
        $facebook_secret="01173688553a76ca5d136f358d83393e";
        $facebook_admin_ID="568870659";
        $site_production_status="LOCAL"; //LIVE if production environment
        //the next three are sagepay variables
        $sage_pay_vendor_name="socialcommerce";
        $sage_pay_connect_string="https://test.sagepay.com/Simulator/VSPServerGateway.asp?Service=VendorRegisterTx";
        //$sage_pay_connect_string="https://test.sagepay.com/showpost/showpost.asp";
        $host="90.207.104.70";
        $site_name="Northumbrian Video";
        //!IMPORTANT - set error reporting to avoid geppy localhost error where php.ini setting was not recognised
        error_reporting(E_ALL);
        $analytics_ID="UA-22611076-1";
    }
    elseif ($_SERVER["HTTP_HOST"]=="lovevideotours.excitedstatelaboratory.com")
    {
        $facebook_appid="211560658870593";
        $facebook_secret="01173688553a76ca5d136f358d83393e";
        $facebook_admin_ID="568870659";
        $site_production_status="STAGE"; //LIVE if production environment
        $sage_pay_vendor_name="socialcommerce";
        $sage_pay_connect_string="https://test.sagepay.com/Simulator/VSPServerGateway.asp?Service=VendorRegisterTx";
        //$sage_pay_connect_string="https://test.sagepay.com/showpost/showpost.asp";
        $host="lovevideotours.excitedstatelaboratory.com";
        $http_address="http://lovevideotours.excitedstatelaboratory.com";
        $site_name="Northumbrian Video";
        //!IMPORTANT - set error reporting to avoid geppy localhost error where php.ini setting was not recognised
        error_reporting(E_ALL);
        //$analytics_ID="UA-24194494-3";
    }
    else
    {
        $analytics_ID="UA-23477650-1";
        $site_production_status="LIVE";
        $http_address="http://lovevideotours.com";
        $host="lovevideotours.com";
    }
    if (isset($_COOKIE["device"]))
        $device=$_COOKIE["device"];
    else
        //SET DEVICE BASED ON USER AGENT
        $device=check_device();
    $device="";
    //remove any nonsense put in by users, such as "' OR 1=1"
    $_REQUEST=strip_request_values($_REQUEST);
    session_start();
?>
