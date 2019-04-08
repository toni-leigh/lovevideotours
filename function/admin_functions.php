<?php
    function adnav_bar()
    {
        $anb="";
        $anb.="<div id='admin_nav'>";
        $anb.="<span class='an_section'>";
        $anb.="<a href='/control-room/videoitem/create/1'><span class='an_link'>Create Video Item</span></a>";
        $anb.="<a href='/control-room/videoitem/edit'><span class='an_link'>Edit Video Item(s)</span></a>";
        $anb.="</span>";
        $anb.="<span class='an_section'>";
        $anb.="<a href='/control-room/user/create'><span class='an_link'>Create User</span></a>";
        $anb.="<a href='/control-room/user/edit'><span class='an_link'>Edit User(s)</span></a>";
        $anb.="</span>";
        $anb.="<span class='an_section'>";
        $anb.="<a href='/control-room/love-video-tours-blog/create'><span class='an_link'>Create Blog Post</span></a>";
        $anb.="<a href='/control-room/love-video-tours-blog/edit'><span class='an_link'>Edit Blog Post(s)</span></a>";
        $anb.="</span>";
        $anb.="<span class='an_section'>";
        $anb.="<a href='/control-room/sales-list'><span class='an_link'>Sales List</span></a>";
        $anb.="</span>";
        $anb.="<form method='post' action=''/>";
        $anb.="<input type='hidden' name='logout' value='logout'/>";
        $anb.="<input id='logout' class='submit' type='submit' name='submit' value='Logout'/>";
        $anb.="</form>";
        $anb.="</div>";
        return $anb;
    }
    function response_message($resm="")
    {
        $rm="<span id='message_panel'>";
        if (isset($_SESSION["response_message"]))
            $rm.=$_SESSION["response_message"];
        else
            $rm.=$resm;
        $rm.="</span>";
        unset($_SESSION["response_message"]);
        return $rm;
    }
    function video_item_form($item,$item_type,$errors,$success=null)
    {
        echo    "<div class='l_heading'>";
        if (isset($item)) $heading="Edit ".$item["itemName"]; else $heading="Create VideoItem";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>".$heading."</span>";
        echo    "</div>";
        if (isset($success))
            if ($success==0)
                echo response_message("<span class='red'>please check your form for errors</span>");
            else
                echo response_message("<span class='green'>".$item["itemName"]." saved correctly</span>");
        else
            echo response_message();
        echo "<div id='video_item_form' class='ad_form'>";
        echo "<form method='post' action=''>";
        echo    "<input type='hidden' name='itemID' value='".$item["itemID"]."'/>";
        echo    "<input type='hidden' name='itemTypeID' value='".$item_type["itemTypeID"]."'/>";
        if (isset($_GET["user_ID"]))
            $user_ID=$_GET["user_ID"];
        else
            $user_ID=$item["userID"];
        echo    "<input type='hidden' name='userID' value='".$user_ID."'/>";
        echo text_field(array("id"=>"itemName","label"=>"Video Item Name","val"=>$item["itemName"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemTweet","label"=>"Video Item Short Description","val"=>$item["itemTweet"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemTags","label"=>"Video Item Tags","val"=>$item["itemTags"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemHTML","label"=>"Video Main Body Text","val"=>$item["itemHTML"],"err"=>$errors,"type"=>"textarea"));
        //gets the item feature set
        if (is_numeric($item["itemID"])) $item_features=get_this_video_item_features($item["itemID"]);
        else $item_features=get_this_video_item_features(0);        
        $checked=array();
        //converts the item feature set to an array - if errors then extract them from post so as not to lose user entered data, else use the $item values
        if (isset($errors))
        {
            //use the $item aka $_POST checked values - so as not to lose a users entered values if they get another field wrong
            $features=get_features();
            while ($feature=mysql_fetch_array($features)) if ($item[$feature["featureID"]."feature"]=="on") $checked[]=$feature["featureID"];
        }
        else while ($item_feature=mysql_fetch_array($item_features)) $checked[]=$item_feature["featureID"];  
        //gets the full feature set
        $features=get_features(null,"all");
        echo "<div id='feature_checks' class='stop_select'>";
        echo "<span class='form_field_header'><span class='form_field_label'>Facilities</span></span>";
        $fc=0;
        while ($feature=mysql_fetch_array($features))
        {
            if (in_array($feature["featureID"],$checked))
            {
                $ch=" checked='checked' ";
                echo "<div id='".$feature["featureID"]."f' title='".$feature["featureText"]."' class='fcheck checked' onclick='toggle_fcheck(".$feature["featureID"].")'>";
            }
            else
            {
                $ch="";
                echo "<div id='".$feature["featureID"]."f' title='".$feature["featureText"]."' class='fcheck unchecked' onclick='toggle_fcheck(".$feature["featureID"].")'>";
            }
            echo "<div class='fcheck_image' style='background-position: -128px -".(($feature["featureID"]-1)*58)."px;'>";
            echo "<input id='".$feature["featureID"]."feature' name='".$feature["featureID"]."feature' type='checkbox' ".$ch."/>";
            //echo "<span class='fcheck_name'>".$feature["featureName"]."</span>";
            echo "</div>";
            echo "</div>";
            if ($fc%7<6) echo item_spacer();
            $fc++;
        }
        echo "</div>";
        echo "<span id='narrow_field_panel'>";
        if (super_admin())
        {
            echo text_field(array("id"=>"nearestTown","label"=>"Nearest Town","val"=>$item["nearestTown"],"err"=>$errors,"type"=>"text"));
            echo text_field(array("id"=>"county","label"=>"County","val"=>$item["county"],"err"=>$errors,"type"=>"text"));
            echo select_set(array("id"=>"category","label"=>"Category","set"=>get_master_categories($item_type["itemTypeID"]),"val"=>$item["categoryID"],"err"=>$errors));
        }
        else
        {
            echo "<input type='hidden' name='categoryID' value='".$item["categoryID"]."'/>";
            echo "<input type='hidden' name='nearestTown' value='".$item["nearestTown"]."'/>";
            echo "<input type='hidden' name='county' value='".$item["county"]."'/>";
        }
        if (is_numeric($item["sleeps"])&&$item["sleeps"]>0) $item_sleeps=$item["sleeps"]; else $item_sleeps=0;
        if (is_numeric($item["minPrice"])&&$item["minPrice"]>0) $item_min_price=$item["minPrice"]; else $item_min_price=0;
        if (is_numeric($item["maxPrice"])&&$item["maxPrice"]>0) $item_max_price=$item["maxPrice"]; else $item_max_price=0;
        echo text_field(array("id"=>"sleeps","label"=>"Sleeps","val"=>$item_sleeps,"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"minPrice","label"=>"Minimum Price (p/w for holiday let)","val"=>$item_min_price,"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"maxPrice","label"=>"Maximum Price (p/w for holiday let)","val"=>$item_max_price,"err"=>$errors,"type"=>"text"));
        echo "</span>";
        echo "</span>";
        echo "<span id='map_input'>";
        if (super_admin())
        {
            echo    "<span id='map_inputs'>";
            echo        "<span class='map_input_latitude'>";
            if (!is_numeric($item["latitude"])) $latitude="55.54747155125705"; else $latitude=$item["latitude"];
            if (!is_numeric($item["longitude"])) $longitude="-2.0096560058593695"; else $longitude=$item["longitude"];
            echo text_field(array("id"=>"latitude","label"=>"","val"=>$latitude,"err"=>$errors,"type"=>"hidden"));
            echo        "</span>";
            echo        "<span class='map_input_longitude'>";
            echo text_field(array("id"=>"longitude","label"=>"","val"=>$longitude,"err"=>$errors,"type"=>"hidden"));
            echo        "</span>";
            echo    "</span>";
        }
        else
        {
            echo "<input type='hidden' name='latitude' value='".$item["latitude"]."'/>";
            echo "<input type='hidden' name='longitude' value='".$item["longitude"]."'/>";
        }
        echo    "<span id='map_canvas_back_office'></span>";
        echo "</span>";
        if (super_admin())
            echo text_field(array("id"=>"videoSRC","label"=>"Video File Name Identifier","val"=>$item["videoSRC"],"err"=>$errors,"type"=>"text"));
        echo    "<span class='submit_button_row'><input class='submit right' type='submit' name='submit' value='Save Video Item'/></span>";
        echo "</form>";
        echo "</div>";
        echo "<div id='video_item_instr' class='instr'>&nbsp;";
        echo "</div>";
    }
    function ad_item_panel($item)
    {
        $video_item_image=mysql_fetch_array(get_images("item",$item["itemID"]));
        $aip="";
        $aip.="<div class='ad_ipanel'>";
        if (strlen($item["itemName"])>30) $iname=substr($item["itemName"],0,30); else $iname=$item["itemName"];
        $aip.="<span class='ad_ipanel_name'>".$iname."</span>";
        if ($video_item_image["tinySquarePath"]=="")
            $video_image_path="/img/missing60.png";
        else
            $video_image_path="/".$video_item_image["tinySquarePath"];
        $aip.="<div class='ad_ipanel_image'><img src='".$video_image_path."' width='60' height='60'/></div>";
        $aip.="<div class='ad_ipanel_links'>";
        $aip.="<span class='ad_ipanel_edit'><a href='/control-room/videoitem/edit/".$item["itemID"]."'>EDIT DETAILS</a></span>";
        $aip.="<span class='ad_ipanel_images'><a href='/image-upload/videoitem/".$item["itemID"]."'>IMAGES</a></span>";
        $aip.="</div>";
        $aip.="</div>";
        return $aip;
    }
    
    function build_lvt_blog_form($item,$item_type,$errors,$success)
    {
        echo    "<div class='l_heading'>";
        if (isset($item)) $heading="Edit ".$item["itemName"]; else $heading="Create Blog";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>".$heading."</span>";
        echo    "</div>";
        if (isset($success))
            if ($success==0)
                echo response_message("<span class='red'>please check your form for errors</span>");
            else
                echo response_message("<span class='green'>".$item["itemName"]." saved correctly</span>");
        else
            echo response_message();
        echo "<div id='admin_form'>";
        echo "<form method='post' action=''>";
        echo    "<input type='hidden' name='itemID' value='".$item["itemID"]."'/>";
        echo    "<input type='hidden' name='itemTypeID' value='".$item_type["itemTypeID"]."'/>";
        echo text_field(array("id"=>"itemName","label"=>"Blog Post Name","val"=>$item["itemName"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemTweet","label"=>"Blog Post Short Description","val"=>$item["itemTweet"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemTags","label"=>"Blog Post Tags","val"=>$item["itemTags"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"itemHTML","label"=>"Video Main Body Text","val"=>$item["itemHTML"],"err"=>$errors,"type"=>"textarea"));
        echo "<div id='ad_bcat'>";
        echo select_set(array("id"=>"category","label"=>"Category","set"=>get_master_categories($item_type["itemTypeID"]),"val"=>$item["categoryID"],"err"=>$errors));
        echo "</div>";
        echo text_field(array("id"=>"videoFileName","label"=>"Video File Name Identifier","val"=>$item["videoFileName"],"err"=>$errors,"type"=>"text"));
        echo    "<span class='submit_button_row'><input class='submit' type='submit' name='submit' value='Save Blog Post'/></span>";
        echo "</form>";
        echo "</div>";
    }
    function lvt_blog_row($item)
    {
        $bimage=mysql_fetch_array(get_images("item",$item["itemID"]));
        $aip="";
        $aip.="<div class='ad_ipanel'>";
        if (strlen($item["itemName"])>30) $iname=substr($item["itemName"],0,30); else $iname=$item["itemName"];
        $aip.="<span class='ad_ipanel_name'>".$iname."</span>";
        if ($bimage["tinySquarePath"]=="")
            $bimage_path="/img/missing60.png";
        else
            $bimage_path="/".$bimage["tinySquarePath"];
        $aip.="<div class='ad_ipanel_image'><img src='".$bimage_path."' width='60' height='60'/></div>";
        $aip.="<div class='ad_ipanel_links'>";
        $aip.="<span class='ad_ipanel_edit'><a href='/control-room/love-video-tours-blog/edit/".$item["itemID"]."'>EDIT DETAILS</a></span>";
        $aip.="<span class='ad_ipanel_images'><a href='/image-upload/love-video-tours-blog/".$item["itemID"]."'>IMAGES</a></span>";
        $aip.="</div>";
        $aip.="</div>";
        return $aip;
    }
    
    function display_user_form($user=null,$errors=null,$success=null)
    {
        echo    "<div class='l_heading'>";
        if (isset($user)) $heading="Edit ".$user["displayName"]; else $heading="Create User";
        echo        "<span class='circle_icon margin_right' style='background-position: -266px -348px;'></span><span class='l_heading_text'>".$heading."</span>";
        echo    "</div>";
        if (isset($success))
            if ($success==0)
                echo response_message("<span class='red'>please check your form for errors</span>");
            else
                echo response_message("<span class='green'>User Details saved correctly</span>");
        else
            echo response_message();
        echo "<div id='centre_admin_form'>";
        echo    "<form method='post' action=''>";
        echo        "<input type='hidden' name='userID' value='".$user["userID"]."'/>";
        echo text_field(array("id"=>"displayName","label"=>"Display Name(s)","val"=>$user["displayName"],"err"=>$errors,"type"=>"text"));
        echo text_field(array("id"=>"userHTML","label"=>"Introduce Yourself","val"=>$user["userHTML"],"err"=>$errors,"type"=>"text"));
        echo email_field(array("email"=>$user["email"],"err"=>$errors));
        if ($errors) $password=$_POST["password"];
        else $password="";
        echo password_field(array("password"=>$password,"err"=>$errors));
        echo        "<span class='submit_button_row'><input class='submit_button' type='submit' name='submit' value='Save User'/></span>";
        echo    "</form>";
        echo "</div>";
    }
    function get_owner_users()
    {
        $dev=0;
        $user_string="select * from User, CottageOwner where User.userID=CottageOwner.userID order by User.displayName asc";
        $user_query=site_query($user_string,"get_owner_users()");
        return $user_query;
    }
    function user_row($user)
    {
        $user_image=mysql_fetch_array(get_images("user",$user["userID"]));
        $aip.="<div class='ad_ipanel'>";
        if (strlen($user["displayName"])>30) $uname=substr($user["displayName"],0,30); else $uname=$user["displayName"];
        $aip.="<span class='ad_ipanel_name'>".$uname."</span>";
        if ($user_image["tinySquarePath"]=="")
            $user_image="/img/missing60.png";
        else
            $user_image="/".$user_image["tinySquarePath"];
        $aip.="<div class='ad_ipanel_image'><img src='".$user_image."' width='60' height='60'/></div>";
        $aip.="<div class='ad_ipanel_links'>";
        $aip.="<span class='ad_ipanel_edit'><a href='/control-room/user/edit/".$user["userID"]."'>EDIT DETAILS</a></span>";
        //$aip.="<span class='ad_ipanel_images'><a href='/image-upload/user/".$user["userID"]."'>IMAGES</a></span>";
        $aip.="<span class='user_new_accomodation'><a href='/control-room/videoitem/create/".$user["userID"]."'>NEW ITEM</a></span>";
        $aip.="</div>";
        $aip.="</div>";
        return $aip;
    }
?>