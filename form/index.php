<?php
    echo dump_include(array("level"=>2,"include_name"=>"index.php"));
    
    // video play stuff
    echo "<script type='text/javascript' src='/jwplayer/jwplayer.js'></script>";
    echo open_script();
    echo    "var aplay='false';";
    echo    "var autoplay=0;";
    echo close_script();
    
    // video player
    echo "<div class='home_list' id='home_page_vids'>";
    /* echo    "<div class='l_heading'>";
    echo        "<span class='circle_icon margin_right' style='background-position: -266px -58px;'></span><span class='l_heading_text'>OUR LATEST VIDEO</span>";
    echo    "</div>"; */
      
    // define the vid panel html
    $video_items=get_items(array("i_type"=>3,"extra_where"=>" videoSrc!='' ","order_by"=>" itemCreated desc "));
    $vis=array();
    $vis[]=null;
    while ($vi=mysql_fetch_array($video_items))
    {
        $vis[]=$vi;
    }
    $vcount=mysql_num_rows($video_items);
    $vph="";
    $vplay_script.="<script type='text/javascript'>";
    $wc=0;
    for ($x=1;$x<count($vis);$x++)
    {
        // scrollers
        if (isset($vis[$x+2]))
        {
            $next_vid_id=$x+2;
            $next_vid_src=$vis[$x+2]["videoSRC"];
        }
        else
        {
            $next_vid_id=0;
            $next_vid_src="";
        }
        $vph.="<div class='fs_image_panel home_page_vid'>";
        $vph.="<div class='home_scroll'>";
        if ($x==1)
        {
            $vph.="<div class='home_vid_left'>";
            $vph.="<span class='circle_icon margin_right' style='background-position: -266px -464px;'></span><span class='left grey_orange ivid_link'>previous</span>";
        }
        else
        {
            $vph.="<div class='home_vid_left activated' onclick='home_vid(\"left\",0,0,0,\"\")'>";
            $vph.="<span class='circle_icon margin_right' style='background-position: -266px -232px;'></span><span class='left orange ivid_link'>previous</span>";
        }
        $vph.="</div>";
        if ($x==$vcount)
        {
            $vph.="<div class='home_vid_right'>";
            $vph.="<span class='circle_icon margin_left right_override' style='background-position: -266px -493px;'></span><span class='right grey_orange ivid_link'>next</span>";
        }
        else
        {
            $vph.="<div class='home_vid_right activated' onclick='home_vid(\"right\",".$x.",".$vcount.",".$next_vid_id.",\"".$next_vid_src."\")'>";
            $vph.="<span class='circle_icon margin_left right_override' style='background-position: -266px -261px;'></span><span class='right orange ivid_link'>next</span>";
        }
        $vph.="</div>";
        $vph.="</div>";
        
        $vph.="<div id='media_player".$x."'>JW Player goes here";
        if ($x<=2)
        {
            $vplay_script.="jwplayer('media_player".$x."').setup({";
            $vplay_script.="flashplayer: '/jwplayer/player.swf',";
            $vplay_script.="width: '940',";
            $vplay_script.="height: '553',";
            $vplay_script.="autostart: aplay,";
            $vplay_script.="backcolor: '000000',";
            $vplay_script.="frontcolor: 'de853e',";
            $vplay_script.="lightcolor: '68bfb7',";
            $vplay_script.="screencolor: '000000',";
            $vplay_script.="controlbar: 'bottom',";
            $vplay_script.="file: 'http://www.youtube.com/watch?v=".$vis[$x]["videoSRC"]."',";
            $vplay_script.="image: '/img/preview.jpg'";
            $vplay_script.="});";
        }
        $vph.="</div>";
        $vph.="<a class='home_vid_link' href='".build_item_link($vis[$x])."'>view more ...</a>";
        $vph.="</div>";
        $wc+=940;
    }
    $vplay_script.="</script>";
    
    // output the images and the video, all in one div that will remain partly obscured ready to slide on click
    echo "<div id='fs_viewer_row'>";
    echo    "<div id='fs_viewer_window'>";
    echo       "<div id='fs_image_vid' style='width:".$wc."px'>";
    // images, using above array
    echo            $vph;
    echo        "</div>";
    echo    "</div>";
    echo "</div>";
    echo "</div>";
    echo "<div class='home_list' id='free_list'>";
    echo    "<div class='l_heading'>";
    echo        "<span class='circle_icon margin_right' style='background-position: -266px -406px;'></span><span class='l_heading_text'>LATEST BLOG POSTS</span>";
    echo    "</div>";
    $blogs=get_items(array("i_type"=>5,"extra_where"=>"","order_by"=>" itemCreated desc ","limit"=>4));
    echo    "<div class='i_row4'>";
    $c=0;
    while ($blog=mysql_fetch_array($blogs))
    {
        echo home_item_panel(get_item(array("i_ID"=>$blog['itemID'],"i_type"=>5)));
        $c++;
        if ($c<4)
        {
            echo item_spacer();
        }
    }
    echo    "</div>";
    echo "</div>";
    echo "<div class='home_list' id='free_list'>";
    echo    "<div class='l_heading'>";
    echo        "<span class='circle_icon margin_right' style='background-position: -266px -116px;'></span><span class='l_heading_text'>FEATURED THINGS TO DO</span>";
    echo    "</div>";
    echo    "<div class='i_row4'>";
    echo home_item_panel(get_item(array("i_ID"=>20,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>36,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>19,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>99,"i_type"=>3))); 
    echo    "</div>";
    echo    "<div class='i_row4'>";
    echo home_item_panel(get_item(array("i_ID"=>25,"i_type"=>3))).item_spacer();
    echo home_item_panel(get_item(array("i_ID"=>7,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>26,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>24,"i_type"=>3)));
    echo    "</div>";
    echo    "<div class='item_row4'>";
    echo home_item_panel(get_item(array("i_ID"=>108,"i_type"=>3))).item_spacer(); 
    echo home_item_panel(get_item(array("i_ID"=>40,"i_type"=>3))).item_spacer();  
    echo home_item_panel(get_item(array("i_ID"=>38,"i_type"=>3))).item_spacer();  
    echo home_item_panel(get_item(array("i_ID"=>23,"i_type"=>3)));  
    echo    "</div>";
    echo "</div>";
    echo "<div class='home_list' id='cottage_list'>";
    echo    "<div class='l_heading'>";
    echo        "<span class='circle_icon margin_right' style='background-position: -266px -58px;'></span><span class='l_heading_text'>FEATURED PLACES TO STAY</span>";
    echo    "</div>";
    echo    "<div class='i_row4'>";   
    echo home_item_panel(get_item(array("i_ID"=>155,"i_type"=>3))).item_spacer();   
    echo home_item_panel(get_item(array("i_ID"=>144,"i_type"=>3))).item_spacer();       
    echo home_item_panel(get_item(array("i_ID"=>141,"i_type"=>3))).item_spacer();       
    echo home_item_panel(get_item(array("i_ID"=>143,"i_type"=>3)));       
    /* echo home_item_panel(get_item(array("i_ID"=>127,"i_type"=>3))); */
    echo     "</div>";
    echo "</div>";
    
    echo $vplay_script;
?>