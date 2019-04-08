<?php
    function product_form($in)
    {
        echo dump_include(array("level"=>3,"include_name"=>"product_functions.php"));
        $pf="<div id='product_admin_form' class='form'>";
        $pf.="<form method='post' action=''>";
        $pf.="<input type='hidden' name='itemID' value='".$in["item"]["itemID"]."'/>";
        $pf.="<input type='hidden' name='itemTypeID' value='".$in["i_type"]["itemTypeID"]."'/>";
        $pf.=text_field(array("id"=>"itemName","label"=>"Product Name","val"=>$in["item"]["itemName"],"err"=>$in["err"],"type"=>"text"));
        $pf.=text_field(array("id"=>"itemTweet","label"=>"Product Short Description","val"=>$in["item"]["itemTweet"],"err"=>$in["err"],"type"=>"text"));
        $pf.=text_field(array("id"=>"itemTags","label"=>"Product Tags","val"=>$in["item"]["itemTags"],"err"=>$in["err"],"type"=>"text"));
        $pf.=text_field(array("id"=>"itemHTML","label"=>"Product Body Text","val"=>$in["item"]["itemHTML"],"err"=>$in["err"],"type"=>"tinymce"));
        $categories=get_master_categories($in["i_type"]["itemTypeID"]);
        $pf.=select_set(array("id"=>"category","label"=>"Product Category","set"=>$categories,"val"=>$in["item"]["categoryID"])); 
        $pf.="<input id='save_product_submit' class='submit_button button' type='submit' name='submit' value='save product'/>";
        $pf.="</form>";
        $pf.="</div>";
        return $pf;
    }
    function product_list($in)
    {
        $counter=0;
        $row_counter=0;
        $pl="";
        $product_count=mysql_num_rows($in["products"]);
        while ($product=mysql_fetch_array($in["products"]))
        {
            // this is set to add classes to first and last in row
            $extra_panel_class="";
            if ($counter%$in["width"]==0)
            {
                $row_counter++;
                if ($row_counter==$in["depth"]||($product_count-$counter)/$in["width"]<1) $row_ID=" id='bottom_row' "; else $row_ID="";
                if ($product_count>1) $extra_panel_class=" product_row_first ";                
                $pl.="<div ".$row_ID." class='item_row".$in["width"]." right'>";
            }
            if ($counter%$in["width"]==$in["width"]-1) $extra_panel_class=" product_row_last ";
            $product_images=get_images("item",$product["itemID"]);
            $product_image=mysql_fetch_array($product_images);
            $pl.="<div class='item_panel product_panel ".$extra_panel_class." left'>";
            $pl.="<a href='".build_item_link($product)."'>";
            $pl.="<img src='/".$product_image["mediumSquarePath"]."' alt='Thumbnail image of ".$product["itemName"].", links to details page' width='200' height='200'/>";
            $pl.="<span class='item_panel_name'>".$product["itemName"]."</span>";
            $pl.="<span class='item_panel_tweet'>".$product["itemTweet"]."</span>";
            $pl.="</a>";
            /*if (is_numeric($in["basket"]))
                $pl.=basket_button(array("product"=>$product,"list_button"=>1)); */  
            $pl.="</div>";          
            if ($counter%$in["width"]==$in["width"]-1||$counter+1==$product_count) $pl.="</div>";
            $counter++;
        }
        return $pl;
    }
    function product_page($item)
    {
        $pp="";
        $pp.="<div id='ptop' class='left'>";
        $pp.="<div id='pimages' class='left'>";
        $pp.="<div id='pimage' class='left'>";
        $images=get_images("item",$item["itemID"]);
        $image=mysql_fetch_array($images);
        $pp.="<img src='/".$image["mediumSquarePath"]."' alt='image of ".$item["itemName"]."' width='380' height='380'/>";
        $pp.="</div>";
        $pp.="<div id='pthumbnails' class='left'>";
        mysql_data_seek($images,0);
        $counter=1;
        while ($image=mysql_fetch_array($images))
        {
            $pp.="<div class='pthumbnail left'>";
            $pp.="<img src='/".$image["tinySquarePath"]."' alt='image of ".$item["itemName"]."' width='60' height='60'/>";
            $pp.="</div>";
            if ($counter<5) $pp.="<div class='pthumbnail_spacer left'>&nbsp;</div>";
            if ($counter==5) break;
            $counter++;
        }
        $pp.="</div>";
        $pp.="</div>";
        $pp.="<div id='pbasket' class='right'>";
        $pp.=basket_button(array("product"=>$item,"err"=>$fail_quantity));
        $pp.="</div>";
        $pp.="</div>";
        $pp.="<div id='product_share' class='left'>";
        //recommend buttons
        if ($_SESSION["user"])
            $pp.=build_social_action_button(array("e_ID"=>$item["itemID"],"e_type"=>"item","e_stype"=>"product","a_type"=>"recommend"));
        $pp.="";
        $pp.="</div>";
        $pp.="<div id='product_details' class='left'>";
        $pp.=stripslashes(cleanup_tinymce_output($item["itemHTML"]));
        $pp.="</div>";
        return $pp;
    }
    function product_details_nav($in)
    {
        $product_images=get_images("item",$in["item"]["itemID"]);
        $image_count=mysql_num_rows($product_images);
        if ($image_count==1) $images="1 image"; else $images=$image_count." images";
        $variations=get_product_variations(array("i_ID"=>$in["item"]["itemID"]));
        $var_count=mysql_num_rows($variations);
        if ($var_count==1) $vars="1 variation"; else $vars=$var_count." variations";
        $pdn="<span id='product_details_edit_header' class='left'>";
        $pdn.="<a href='/product/edit'><span id='back_to_products' class='admin_product_details_button button left'>&lt; - Back to Product List</span></a>";
        $pdn.=product_details_links(array("item"=>$in["item"],"ilink_txt"=>$images,"vlink_txt"=>$vars));
        $pdn.="</span>";
        return $pdn;
    }
    function product_details_links($in)
    {
        $pdl="<a href='/product/edit/".$in["item"]["itemID"]."'><span id='admin_product_details_button' class='admin_product_details_button button left'>details</span></a>";
        $pdl.="<a href='/image-upload/product/".$in["item"]["itemID"]."'><span id='admin_product_images_button' class='admin_product_details_button button left'>".$in["ilink_txt"]."</span></a>";
        $pdl.="<a href='/product/variations/".$in["item"]["itemID"]."'><span id='admin_product_variations_button' class='admin_product_details_button button left'>".$in["vlink_txt"]."</span></a>";
        return $pdl;
    }
    function admin_product_list($products)
    {
        while ($product=mysql_fetch_array($products))
        {
            $product_images=get_images("item",$product["itemID"]);
            $image_count=mysql_num_rows($product_images);
            if ($image_count==1) $images="1 image"; else $images=$image_count." images";
            $variations=get_product_variations(array("i_ID"=>$product["itemID"]));
            $var_count=mysql_num_rows($variations);
            if ($var_count==1) $vars="1 variation"; else $vars=$var_count." variations";
            $product_image=mysql_fetch_array($product_images);
            echo "<div class='admin_product_row left'>";
            echo    "<img class='left' src='/".$product_image["mediumSquarePath"]."' width='80' height='80' alt='back office product image'/>";
            echo    "<div class='admin_product_name right'>";
            echo        $product["itemName"];
            echo    "</div>";
            echo    "<div class='admin_product_shortdesc left'>";
            echo        $product["itemTweet"]."&nbsp;";
            echo    "</div>";
            echo    "<div class='admin_product_links right'>";
            echo        product_details_links(array("item"=>$product,"ilink_txt"=>$images,"vlink_txt"=>$vars));
            echo    "</div>";
            echo "</div>";
        }
    }
    function get_main_variation($item_ID)
    {
        $main=site_query("select * from ProductVariation where itemID=".$item_ID." and main=1","get_main_variation()");
        if (mysql_num_rows($main))
            $main_var=mysql_fetch_array($main);
        else
            $main_var=array("variationID"=>0);
        return $main_var;
    }
    function get_variation_text($v_ID)
    {
        $vstring="";
        $variation_values=site_query("select * from ProductVariationValue, VariationType, VariationValue where ProductVariationValue.productVariationID=".$v_ID." and ProductVariationValue.variationTypeID=VariationType.variationTypeID and ProductVariationValue.variationValueID=VariationValue.variationValueID","get pvvalues variation_selector()");
        while ($vvalue=mysql_fetch_array($variation_values))
        {
            if ($vvalue["variationTypeName"]=="pack quantity")
                $vstring.="pack of ".$vvalue["variationValue"].";";
            elseif (strtolower($vvalue["variationTypeName"])=="colour"||strtolower($vvalue["variationTypeName"])=="color")
                $vstring.=" ".$vvalue["variationValue"]."; ";
            else
                $vstring.=$vvalue["variationTypeName"]." - ".$vvalue["variationValue"]."; ";
        }
        $vstring=substr($vstring,0,-2);
        return $vstring;
    }
?>