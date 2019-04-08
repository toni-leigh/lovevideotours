<?php
    $dev=0;
    dev_dump($_FILES,"Files Array",$dev);
    dev_dump($_POST,"Post Array",$dev);
    dev_dump($_GET,"Post Array",$dev);
    //upload larger files, adjust php file limit for this file only
    ini_set("memory_limit","200M");
    //get the item details from the URL
    if (is_numeric($_GET["item_ID"]))
    {
        $item=get_item(array("i_type"=>$_GET["item_type"],"i_ID"=>$_GET["item_ID"]));
        $item_ID=$item["itemID"];
        $item_name=$item["itemName"];
        $action_reload_URL="/image-upload/".$item["itemType"]."/".$item["itemID"];
        $entity_type="item";
        $item_type=get_item_type($_GET["item_type"]);
        $random_key=md5(time().$_SERVER["REMOTE_ADDR"]);
        $item_file_name=preg_replace("/[^a-zA-Z0-9]+/i","",$item_name);
        $square_sizes=array();
        $square_sizes["tinySquare"]=$item_type["tinySquare"];$square_sizes["mediumSquare"]=$item_type["mediumSquare"];
        $square_sizes["largeSquare"]=$item_type["largeSquare"];$square_sizes["massiveSquare"]=$item_type["massiveSquare"];
        $scale_sizes=array();
        $scale_sizes["tinyScale"]=$item_type["tinyScale"];$scale_sizes["mediumScale"]=$item_type["mediumScale"];
        $scale_sizes["largeScale"]=$item_type["largeScale"];$scale_sizes["pageScale"]=$item_type["pageScale"];$scale_sizes["zoomScale"]=$item_type["zoomScale"];
        $item_type=$item_type["itemType"];
    }
    else
    {
        dev_dump("Loose Bit","",$dev);
        //loose image
        $loose_image=1;
        $item_ID=0;
        $item_name="Loose";
        $action_reload_URL="/image-upload/loose-image";
        $entity_type="loose";
        $item_type="loose";
        $random_key=md5(time().$_SERVER["REMOTE_ADDR"]);
        if (isset($_SESSION["image_name"]))
        {
            //use the session variable if it is present as this is not the first traverse and the form is empty
            $item_file_name=$_SESSION["image_name"];
        }
        else
        {
            if (strlen($_POST["imageName"])>0)
            {
                $item_file_name=preg_replace("/[^a-zA-Z0-9]+/i","",$_POST["imageName"]);
            }
            else
            {
                $item_file_name_array=explode(".",$_FILES['image']['name']);  
                $item_file_name=$item_file_name_array[0];     
            }
        }
        $square_sizes=array();
        $square_sizes["tinySquare"]=60;$square_sizes["mediumSquare"]=140;
        $square_sizes["largeSquare"]=300;$square_sizes["massiveSquare"]=500;
        $scale_sizes=array();
        $scale_sizes["tinyScale"]=220;$scale_sizes["mediumScale"]=460;
        $scale_sizes["largeScale"]=620;$scale_sizes["pageScale"]=700;$scale_sizes["zoomScale"]=1260;
    }
    dev_dump($item_file_name,"Item File Name",$dev);
    //only assign a new timestamp if the session variable is empty
    if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0)
    {
        $_SESSION['random_key'] = $random_key;
        $_SESSION['user_file_ext']= "";
    }    
    //image
    $max_file = "8";                                                   
    //only one of these image types should be allowed for upload
    $allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
    $allowed_image_ext = array_unique($allowed_image_types);
    $image_ext = "";
    foreach ($allowed_image_ext as $mime_type => $ext)
        $image_ext.= strtoupper($ext)." ";        
    //directory - the directory name must be seperate so the mkdir will work
    $upload_dir = "img/".$item_type;
    $upload_path = $upload_dir."/";  
    $sizes2=$square_sizes;
    $scales2=$scale_sizes;
    //file locations
    $square_locations=array();
    foreach($square_sizes as $key => $value)
        if ($value>0)
            $square_locations[$key]=$upload_dir."/".$item_file_name."_".$square_sizes[$key]."square_".$_SESSION['random_key'].$_SESSION['user_file_ext'];
    $scale_locations=array();
    foreach($scale_sizes as $key => $value)
        if ($value>0)
            $scale_locations[$key]=$upload_dir."/".$item_file_name."_".$scale_sizes[$key]."scale_".$_SESSION['random_key'].$_SESSION['user_file_ext'];
    dev_dump($square_locations,"Square Locations",$dev);
    dev_dump($scale_locations,"Scale Locations",$dev);
    //create the upload directory with the right permissions if it doesn't exist
    if(!is_dir($upload_dir))
    {
        mkdir($upload_dir, 0777);
        chmod($upload_dir, 0777);
    }
    
    //check to see if any images with the same name already exist
    if (file_exists($scale_locations["largeScale"]))
    {
        if(file_exists($square_locations["mediumSquare"]))
        {
            $thumb_photo_exists = "<img src=\"/".$square_locations["mediumSquare"]."\" alt=\"Thumbnail Image\"/>";
        }
        else
        {
            $thumb_photo_exists = "";
        }
        $large_photo_exists = "<img src=\"/".$scale_locations["largeScale"]."\" alt=\"Large Image\"/>";
    }
    else
    {
        $large_photo_exists = "";
        $thumb_photo_exists = "";
    }
    if (isset($_POST["upload"]))
    {
        //get the file information
        $userfile_name = $_FILES['image']['name'];
        $userfile_tmp = $_FILES['image']['tmp_name'];
        $userfile_size = $_FILES['image']['size'];
        $userfile_type = $_FILES['image']['type'];
        $filename = basename($_FILES['image']['name']);
        $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        //check for invalid file errors, wrong type, size etc.
        if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0))
        {
            foreach ($allowed_image_types as $mime_type => $ext)
            {
                //loop through the specified image types and if they match the extension then break out
                //everything is ok so go and check file size
                if(($file_ext==$ext && $userfile_type==$mime_type)||($file_ext=="jpeg"&&$userfile_type=="image/jpeg"))
                {
                    $response_message = "";
                    break;
                }
                else
                {
                    $response_message = "<span class='red'>Only ".$image_ext." images accepted for upload</span>";
                }
            }
            //check if the file size is above the allowed limit
            if ($userfile_size > ($max_file*1048576))
            {
                $response_message = "<span class='red'>Images must be under ".$max_file."MB in size</span>";
            }
    
        }
        else
        {
            $response_message = "<span class='red'>Select an image for upload</span>";
        }
        //Everything is ok, so we can upload the image.
        if (strlen($response_message)==0)
        {
    
            if (isset($_FILES['image']['name']))
            {
                //sort out the file names, with correcct extension and extension hack
                foreach($square_sizes as $key => $value)
                {
                    if ($value>0)
                    {
                        $square_locations[$key].=".".$file_ext;
                        $square_locations[$key]=strip_extra_file_extension($square_locations[$key],$file_ext);
                    }
                }
                //also we need to create the scaled versions of the file here as well as check the filenames
                foreach($scale_sizes as $key => $value)
                {
                    if ($value>0)
                    {
                        $scale_locations[$key].=".".$file_ext;
                        $scale_locations[$key]=strip_extra_file_extension($scale_locations[$key],$file_ext);
                        copy($userfile_tmp, $scale_locations[$key]);
                        chmod($scale_locations[$key], 0777);         
                        $width = getWidth($scale_locations[$key]);
                        $height = getHeight($scale_locations[$key]); 
                        $scale = $value/$width;
                        $uploaded = resizeImage($scale_locations[$key],$width,$height,$scale);
                    }
                }
                //put the file ext in the session so we know what file to look for once its uploaded
                $_SESSION['user_file_ext']=".".$file_ext;                  
                //delete the thumbnail file so the user can create a new one
                if (file_exists($square_locations["mediumSquare"]))
                {
                    unlink($square_locations["mediumSquare"]);
                }
                
            }
            //refresh the page and show the new uploaded image
            if ($loose_image)
                $_SESSION["image_name"]=$item_file_name;
            $_SESSION["response_message"]="<span class='green'>Image uploaded</span>";
            header("location:".$action_reload_URL);
            exit(); 
        }
    }    
    if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0)
    {
        //crop co-ordinates
        $x1 = $_POST["x1"];
        $y1 = $_POST["y1"];
        $x2 = $_POST["x2"];
        $y2 = $_POST["y2"];
        $w = $_POST["w"];
        $h = $_POST["h"];
        //create the four thumbnail images        
        foreach($square_sizes as $key => $value)
        {
            if ($value>0)
            {
                $scale=$value/$w;
                $cropped = resizeThumbnailImage($square_locations[$key],$scale_locations["largeScale"],$w,$h,$x1,$y1,$scale);
            }
        }
        //add details to db       
        $image_save_string="insert into Image (entityID,entityType,";
        foreach($square_sizes as $key => $value) $image_save_string.=$key."Path,";
        foreach($scale_sizes as $key => $value) $image_save_string.=$key."Path,";
        $image_save_string.="imageName)";
        $image_save_string.=" values ";
        $image_save_string.="(".$item_ID.",'".$entity_type."',";
        foreach($square_sizes as $key => $value) $image_save_string.="'".$square_locations[$key]."',";
        foreach($scale_sizes as $key => $value) $image_save_string.="'".$scale_locations[$key]."',";
        $image_save_string.="'".$_POST["image_name"]."')";            
        site_query($image_save_string,"save image paths in php/image_upload.php",$dev);
        //clear the session variables that allow the files to be found throughout the reloads
        unset($_SESSION["random_key"]);
        unset($_SESSION["image_name"]);
        $_SESSION["response_message"]="<span class='green'>Image uploaded and cropped</span>";
        header("location:".$action_reload_URL);
        exit(); 
    }
    if (isset($_GET["remove"]))
    {
        unset($_SESSION['user_file_ext']);
        $large_photo_exists="";
        $thumb_photo_exists="";
        unset($_SESSION["in_first"]);
        $_SESSION["response_message"]="<span class='green'>Wrong image ignored, use the upload function to upload another</span>";
        header("location:".$action_reload_URL);
    }
    //this processes the other form, setting main and removing also
    if (isset($_POST["save_main"]))
    {
        $item_images=get_images("item",$item["itemID"]);
        while ($item_image=mysql_fetch_array($item_images))
        {
            if ($_POST[$item_image["imageID"]."remove"]=="on")
            {
                $update_image=site_query("update Image set removed=1 where imageID=".$item_image["imageID"],"set removed in image upload controller");
                //unlink code can go here if unlink is needed for deleted images
            }
            if ($_POST["main"]==$item_image["imageID"])
                $update_image=site_query("update Image set main=1 where imageID=".$item_image["imageID"],"set main in image upload controller");
            else
                $update_image=site_query("update Image set main=0 where imageID=".$item_image["imageID"],"unset main in image upload controller");
        }
        $response_message="<span class='green'>Set main image and / or delete images successful</span>";
    }
?>
