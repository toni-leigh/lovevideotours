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
    $wc=0;
    for ($x=1;$x<=count($vis);$x++)
    {
        dev_dump($vis[$x]["videoSRC"]);
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
        $vph.="<div id='home_scroll'>";
        if ($x==1)
            $vph.="<div id='home_vid_left'>";
        else
            $vph.="<div id='home_vid_left' onclick='home_vid(\"left\",0,0,0,\"\")'>";
        $vph.="<span class='circle_icon margin_right' style='background-position: -266px -232px;'></span><span class='left orange ivid_link'>previous</span>";
        $vph.="</div>";
        if ($x==$vcount)
            $vph.="<div id='home_vid_right'>";
        else
            $vph.="<div id='home_vid_right' onclick='home_vid(\"right\",".$x.",".$vcount.",".$next_vid_id.",\"".$next_vid_src."\")'>";
        $vph.="<span id='right' class='circle_icon margin_left' style='background-position: -266px -261px;'></span><span class='right orange ivid_link'>next</span>";
        $vph.="</div>";
        $vph.="</div>";
        $vph.="<div id='media_player".$x."'>JW Player goes here";
        if ($x<=2)
        {
            $vph.="<script type='text/javascript'>";
            $vph.="jwplayer('media_player".$x."').setup({";
            $vph.="flashplayer: '/jwplayer/player.swf',";
            $vph.="width: '940',";
            $vph.="height: '553',";
            $vph.="autostart: aplay,";
            $vph.="backcolor: '000000',";
            $vph.="frontcolor: 'de853e',";
            $vph.="lightcolor: '68bfb7',";
            $vph.="screencolor: '000000',";
            $vph.="controlbar: 'bottom',";
            $vph.="file: 'http://www.youtube.com/watch?v=".$vi["videoSRC"]."',";
            $vph.="image: '/img/preview.jpg'";
            $vph.="});";
            $vph.="</script>";
        }
        $vph.="<a class='home_vid_link' href='".build_item_link($vi)."'>view more ...</a>";
        $vph.="</div>";
        $vph.="</div>";
        $wc+=940;
    }
    
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
    echo home_item_panel(get_item(array("i_ID"=>141,"i_type"=>3))).item_spacer();       
    echo home_item_panel(get_item(array("i_ID"=>143,"i_type"=>3))).item_spacer();     
    /* echo home_item_panel(get_item(array("i_ID"=>126,"i_type"=>3))).item_spacer();     
    echo home_item_panel(get_item(array("i_ID"=>127,"i_type"=>3))); */
    echo     "</div>";
    echo "</div>";
?>