<?php
    function blog_form($in)
    {
        echo dump_include(array("level"=>3,"include_name"=>"blog_functions.php"));
        $bf="<div id='blog_admin_form' class='form'>";
        $bf.="<form method='post' action=''>";
        $bf.="<input type='hidden' name='itemID' value='".$in["item"]["itemID"]."'/>";
        $bf.="<input type='hidden' name='itemTypeID' value='".$in["i_type"]["itemTypeID"]."'/>";
        $bf.=text_field(array("id"=>"itemName","label"=>"Blog Name","val"=>$in["item"]["itemName"],"err"=>$in["err"],"type"=>"text"));
        $bf.=text_field(array("id"=>"itemTweet","label"=>"Blog Short Description","val"=>$in["item"]["itemTweet"],"err"=>$in["err"],"type"=>"text"));
        $bf.=text_field(array("id"=>"itemHTML","label"=>"Blog Body Text","val"=>$in["item"]["itemHTML"],"err"=>$in["err"],"type"=>"textarea"));
        $categories=get_master_categories($in["i_type"]["itemTypeID"]);
        $pf.=select_set(array("id"=>"category","label"=>"Blog Category","set"=>$categories,"val"=>$in["item"]["categoryID"])); 
        $bf.="<input id='save_blog_post_submit' class='submit_button button' type='submit' name='submit' value='save blog post'/>";
        $bf.="</form>";
        $bf.="</div>";
        return $bf;
    }
    function blog_list($in)
    {
        $counter=0;
        $bl="";
        $blog_count=mysql_num_rows($in["blogs"]);
        while ($blog=mysql_fetch_array($in["blogs"]))
        {
            if ($counter%$in["width"]==0) $bl.="<div class='item_row".$in["width"]."'>";
            $blog_images=get_images("item",$blog["itemID"]);
            $blog_image=mysql_fetch_array($blog_images);
            $bl.="<div class='item_panel blog_panel'>";
            $bl.="<a href='".build_item_link($blog)."'>";
            $bl.="<img src='/".$blog_image["mediumSquarePath"]."' alt='Thumbnail image of ".$blog["itemName"].", links to details page' width='200' height='200'/>";
            $bl.="<span class='item_panel_name'>".$blog["itemName"]."</span>";
            $bl.="<span class='item_panel_tweet'>".$blog["itemTweet"]."</span>";
            $bl.="</a>";  
            $bl.="</div>";          
            if ($counter%$in["width"]==$in["width"]-1||$counter+1==$blog_count) $bl.="</div>";
            $counter++;
        }
        return $bl;
    }
?>