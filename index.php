<?php
    //MODEL - GLOBAL
    include "function/dev_functions.php";
    include "function/global_functions.php";
    include "function/js_functions.php";
    include "function/initialise.php";
    include "function/action_functions.php";
    include "function/basket_functions.php";
    include "function/comment_functions.php";
    include "function/image_functions.php";
    include "function/item_functions.php";
    include "function/page_functions.php";
    include "function/user_functions.php";
    include "function/videoitem_display_functions.php";
    
    // redirect list-with-us
    if ($_GET["element_reference"]=="list-with-us") header("location:/prices");
    
    //process global form triggers - these are mostly here to support situations where js is turned off    
    if ($_POST["logout"]=="logout")
    {
        if ($_SESSION["user"])
            site_query("insert into AccessLog (userID,host,logType) values (".$_SESSION["user"]["userID"].",'".$_SERVER["REMOTE_ADDR"]."','logout')","index - log users logout time");
        unset($_SESSION["user"]);
        unset($_SESSION["basket_ID"]);
        setcookie("usignin",1,time()-(60*60),"/");
        session_destroy();
        session_start();
    }
    else
        if ($_COOKIE["usignin"]==1) set_user_signed_in(array("userID"=>1),0,"clogin");
    //dump_globals();
    //these common form processors are triggered by actions with jquery equivalents
    if (isset($_POST["follow_recommend"]))
    {
        $entity_ID=$_POST["entity_ID"];
        $entity_type=$_POST["entity_type"];
        $entity_sub_type=$_POST["entity_sub_type"];
        $action_type=$_POST["action_type"];
        include "function/record_basic_social_action.php";
    }
    if (isset($_POST["save_comment_no_js"]))
    {
        if ($_POST["add_comment_box"]!="")
        {
            save_comment(array("e_ID"=>$_POST["entity_ID"],"e_type"=>$_POST["entity_type"],"comment"=>$_POST["add_comment_box"]));    
            record_action(array("e_ID"=>$_POST["entity_ID"],"e_type"=>$_POST["entity_type"],"e_stype"=>$_POST["entity_sub_type"],"a_type"=>"commented"));
        }
    }
    // process the newsletter sign-up
    if (isset($_POST["newsletter_submit"]))
    {
        if (validate_email_format($_POST["newsletter"]))
        {
            $signup_saved=1;
            $check_emails=site_query("select * from NewsletterSignup where email='".$_POST["newsletter"]."'","check for email in nls");
            if (mysql_num_rows($check_emails)<1)
                site_query("insert into NewsletterSignup (email) values ('".$_POST["newsletter"]."')","add email to nls");
        }
        else
        {
            $nls_bademail=1;
        }
    }
    //the user login / register stuff must be included if no user is signed in
    if (!$_SESSION["user"])
    {
        include "function/engage_functions.php";
        include "control/engage_check.php";
        //see header for inclusion of hidden engage form
    }
    //Global Controller Stuff
    //parse URL - defines the page, type, category and loads objects
    //the video item type has no element reference in the URL, to keep it clean
    $vid_type_ID=3;
    if (is_category($_GET["element_reference"],$vid_type_ID))
    {
        $item_type=get_item_type($vid_type_ID);
        include "function/".$item_type["itemType"]."_functions.php";
        //categorised item pages
        $page=get_page(3);
        include "control/get_page_functions.php";
        include "control/item_controller.php";
        include "control/".$item_type["itemType"]."_controller.php";
    }
    elseif (is_item_type($_GET["element_reference"])||$_GET["element_reference"]=="user")
    {
        if (is_item_type($_GET["element_reference"]))
        {
            $item_type=get_item_type($_GET["element_reference"]);
            //categorised item pages
            //create and edit admin options
            if (isset($_GET["admin_divert"]))
            {
                include "function/".$item_type["itemType"]."_functions.php";
                $page=get_page($item_type["adminPageID"]);
                include "control/get_page_functions.php";
                include "control/admin-item_controller.php";
            }
            else
            {
                include "function/".$item_type["itemType"]."_functions.php";
                $page=get_page(3);
                include "control/get_page_functions.php";
                include "control/item_controller.php";
                include "control/".$item_type["itemType"]."_controller.php";
            }
        }
        else
        {
            $page=get_page(40);
            include "control/get_page_functions.php";
            include "control/admin_user_controller.php";
        }
    }
    else
    {
        if ($_GET["element_reference"]=="")
        {
            $page=get_page(1); //home page record
            include "control/get_page_functions.php";
            $entity_ID=$page["pageID"];
            $entity_type="page";
            $page["URL"]="index";
        }
        else
        {
            if (get_page($_GET["element_reference"]))
            {
                //page controller stuff - stays on the index.php
                $page=get_page($_GET["element_reference"]);
                include "control/get_page_functions.php";
                $entity_ID=$page["pageID"];
                $entity_type="page";
                if ($page["controllerInclude"])
                    include "control/".$page["URL"]."_controller.php";
            }
            else
            {
                $page=get_page(2); //user page record
                include "control/get_page_functions.php";
                include "control/user_controller.php";
                //only if the user type is set - other wise this is a 'not found' page
                if ($user_type)
                    include "control/".$user_type."_controller.php";
            }
        }
    }
    include "form/html_open.php";
    //in all cases get the device from user agent string and set this in the session - we fall back on the session value if there is no cookie set
    include "form/head.php";
    //Form
    include "form/page_open.php";
    include $device."form/header.php";
    include $device."form/content.php";
    include $device."form/footer.php";
    include "form/page_close.php";
    include "form/html_close.php";
?>