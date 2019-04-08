<?php
    function videoitem_row($item)
    {
        $video_item_image=mysql_fetch_array(get_images("item",$item["itemID"]));
        echo "<span class='video_item_row'>";
        if ($video_item_image["tinySquarePath"]=="")
            $video_image_path="/img/image_missing60.png";
        else
            $video_image_path="/".$video_item_image["tinySquarePath"];
        echo    "<div class='video_item_image'><img src='".$video_image_path."' width='60' heihgt='60'/></div>";
        echo    "<span class='video_item_name'>".$item["itemName"]."</span>";
        echo    "<span class='video_item_edit'><a href='/control-room/videoitem/edit/".$item["itemID"]."'>EDIT DETAILS</a></span>";
        echo    "<span class='video_item_images'><a href='/image-upload/videoitem/".$item["itemID"]."'>IMAGES</a></span>";
        echo    "<span class='video_item_blog'><a href='/control-room/blog/create/".$item["itemID"]."'>ADD UPDATE</a></span>";
        echo "</span>";
    }
    function get_videoitem_updates($item)
    {
        $item_type=get_item_type(2);
        return get_items(array("i_type"=>$item_type,"extra_where"=>" (blogPropertyID=".$item["itemID"]." or blogPropertyID=0) and User.userID=".$item["userID"]." ","order_by"=>"itemCreated desc"));
    }
    function get_feature_details($feature_ID)
    {
        $feature_string="select * from Feature where featureID=".$feature_ID;
        $feature_query=site_query($feature_string,"get_feature_details()");
        return mysql_fetch_array($feature_query);
    }
    function get_this_video_item_features($item_ID)
    {
        $feature_string="select * from VideoItemFeature, Feature where itemID=".$item_ID." and Feature.featureID=VideoItemFeature.featureID and removed=0";
        $feature_query=site_query($feature_string,"get_this_video_item_features()");
        return $feature_query;
    }
    function get_features($category_ID=null,$all=null)
    {
        if ($category_ID==null)
            $category_append="";
        else
            $category_append="and Item.categoryID=".$category_ID." ";
        if ($all==null)
            $in_clause="where featureID in (select distinct featureID from VideoItemFeature, Item where Item.itemID=VideoItemFeature.itemID and VideoItemFeature.removed=0 and Item.itemDisplay=1 ".$category_append.")";
        else
            $in_clause="";
        $feature_string="select * from Feature ".$in_clause." order by featureName";
        $feature_query=site_query($feature_string,"get_features()");
        return $feature_query;
    }
    /*
     gets all the categories of attraction
    */
    function get_attraction_categories()
    {
        $attraction_categories_string="select * from Category where parentID=5 order by displayOrder";
        $attraction_categories_query=site_query($attraction_categories_string,"get_attraction_categories()",$dev);
        return $attraction_categories_query;
    }
    /*
     gets range of sleeps values based on the sleeps values stored for all current holiday lets
    */
    function get_sleeps_range()
    {
        $sleeps_string="select distinct(sleeps) from Item, VideoItem where Item.itemID=VideoItem.itemID and Item.categoryID=2";
        $sleeps_query=site_query($sleeps_string,"get_sleeps_range()");
        return $sleeps_query;
    }
    function get_attractions()
    {
        $attractions_string="select * from Category, Item, VideoItem where Item.categoryID=Category.CategoryID and Item.itemID=VideoItem.itemID and Category.parentID=5";
        $attractions_query=site_query($attractions_string,"get_attractions()",$dev);
        return $attractions_query;
    }
    /*
     a specific get video items function
     used by category display pages, search results, to get map items and by ajax updates on filter
     
     $in["cats"] = array of sub categories, sub categories won't be returned if their master category isn't present
     $in["price"] = cost to stay in the property for one night
     $in["sleeps"] = number of beds required
     $in["facs"] = array of required facilities
     
     $in["search_term"] = text search term for restriction also, only set by the search box
     $in["order_by"] = will be set by sort buttons to list in order of price
     $in["page_number"] = current page
     $in["map"] = boolean, true if a map then page_number not applied
    */
    function get_video_items($in)
    {
        if (count($in["cats"])==1&&!is_numeric($in["cats"][0])) $in["cats"]=null;
        /*echo "<span style='color:#fff'>";
        dev_dump($in);
        echo "</span>";*/
        $per_page=100000;
        
        // initialise query
        $vstring="";
        $vstring.="select * from Item, VideoItem, Category where Item.itemID=VideoItem.itemID and Item.categoryID=Category.categoryID and Item.itemDisplay=1 ";
        
        // apply restrictions from the 'res' array
        if (is_numeric($in["price"])) $vstring.="and maxPrice<=".$in["price"]." ";
        if (is_numeric($in["sleeps"])) $vstring.="and sleeps>=".$in["sleeps"]." ";
        
        // apply category restriction
        if (count($in["cats"])>0)
        {
            $vstring.="and Item.categoryID in (";
            foreach ($in["cats"] as $c_ID)
                $vstring.=$c_ID.",";
            if ($in["map"]==1) $vstring.="3,"; // also get property on map, always
            $vstring=substr($vstring,0,-1).") ";
        }
        
        // restrict based on required facilities - uses link table so extra complexity      
        if (count($in["facs"])>0)
            foreach($in["facs"] as $f_ID)
                $vstring.="and Item.itemID in (select itemID from VideoItemFeature where featureID=".$f_ID.") ";
                
        // apply search term
        if (isset($in["search_term"]))
        {
            $_SESSION["vitem_set_search"]=1; // tells the traversal stuff that this set is from a search query
            $vstring.="and (";
            if (is_array($in["search_term"]))
            {
                //split term search
                $array_count=count($in["search_term"]);
                $counter=1;
                foreach ($in["search_term"] as $term)
                {
                    $vstring.="Item.itemName regexp '[[:<:]]".$term."[[:>:]]' or Item.itemTweet regexp '[[:<:]]".$term."[[:>:]]' or Item.itemTags regexp '[[:<:]]".$term."[[:>:]]' or VideoItem.nearestTown regexp '[[:<:]]".$term."[[:>:]]' or VideoItem.county regexp '[[:<:]]".$term."[[:>:]]' ";
                    if ($counter<$array_count)
                        $vstring.=" or ";
                    $counter=$counter+1;
                }
            }
            else
            {
                //phrase search
                $vstring.="Item.itemName regexp '[[:<:]]".$in["search_term"]."[[:>:]]' or Item.itemTweet regexp '[[:<:]]".$in["search_term"]."[[:>:]]' or Item.itemTags regexp '[[:<:]]".$in["search_term"]."[[:>:]]' or VideoItem.nearestTown regexp '[[:<:]]".$in["search_term"]."[[:>:]]' or VideoItem.county regexp '[[:<:]]".$in["search_term"]."[[:>:]]' ";
            }
            $vstring.=")";
        }
        
        // order by, defaults to itemName if not present
        if (strlen($in["order_by"])==0) $vstring.="order by promoted, itemName "; else $vstring.="order by promoted, ".$order_by." ";
        
        // limit, a page number based value
        if (!$in["map"])
        {
            // user gets at this value, so catch everything but numbers
            if (is_numeric($in["page_number"])) $page_num=$in["page_number"]; else $page_num=1;
            $start=($page_num-1)*$per_page;
            if ($start<0) $start=0;
            $vstring.="limit ".$start.",".$per_page;
        }
        // return result
        $vquery=site_query($vstring,"get_video_items() - full query output",0);
        
        if (mysql_num_rows($vquery)>0&&!isset($in["skip_traverse"]))
        {
            // load the set of video items into an array in the $_SESSION for traversal from item and full screen pages
            unset($_SESSION["vitem_set"]);
            $c=0;
            if ($_SESSION["item"]["categoryID"]==3)
            {
                while ($vitem=mysql_fetch_array($vquery))
                {
                    if ($vitem["categoryID"]==3)
                    {
                        $_SESSION["vitem_set"][$c]["item"]=$vitem;
                        $c++;
                    }
                }      
            }
            else
            {
                while ($vitem=mysql_fetch_array($vquery))
                {
                    if ($vitem["categoryID"]!=3)
                    {
                        $_SESSION["vitem_set"][$c]["item"]=$vitem;
                        $c++;
                    }
                }    
            }
            // reset, return
            mysql_data_seek($vquery,0);
        }
        //dev_dump(count($_SESSION["vitem_set"]));
        //dev_dump($_SESSION["vitem_set"]);
        return $vquery;
    }
?>