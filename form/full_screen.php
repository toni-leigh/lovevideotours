<?php
    if ($_GET["mode"]=="full_screen")
        echo "<div id='full_screen' class='stop_select' style='display:block;'>";
    else
        echo "<div id='full_screen' class='stop_select' style='display:none;'>";
    // if play is set in URL to autoplay, need a global js variable that will be used by youtube player ready function
    echo open_script();
    if ($_GET["play"]==1)
    {
        echo    "var aplay='true';";
        echo    "var autoplayVid='".$item["videoSRC"]."';";
    }
    else
    {
        echo    "var aplay='false';";
        echo    "var autoplay=0;";
    }
    echo close_script();
    
    echo    "<div id='full_screen_centre'>";
    echo vi_traverse(array("item"=>$item,"mode"=>"fs"));
    // get image paths and sizes into array to use for display
    $landscapes=array();
    $portraits=array();
    $build_temp=array();
    $ips=array(); // this is the main, ordered array for display
    // element width incrementers
    $wc=0;
    $thumbw=0;
    $c=0;
    // add a video array element if video present - this will function as a thumbnail
    if (strlen($item["videoSRC"]))
    {
        $build_temp[$c]["thumb"]="img/vid_thumb.png";
        $landscapes[]=$build_temp[$c]; // vid is classed as landscape
        $c++;
        $thumbw+=90;
        $wc+=940;
    }
    while ($image=mysql_fetch_array($item_images))
    {
        // thumbnail path, sizes and ratio
        $build_temp[$c]["thumb"]=$image["mediumSquarePath"];
        $size=getimagesize($http_address."/".$image["zoomScalePath"]);
        $width=$size[0];
        $height=$size[1];
        $ratio=$width/$height;
        
        // set widths and heights for display, copes with everything from portrait to cinemascape images
        $build_temp[$c]["width"]=ceil($ratio*568);
        $build_temp[$c]["height"]=568;
        if ($build_temp[$c]["width"]>940)
        {
            $wide_ratio=$build_temp[$c]["width"]/940;
            $build_temp[$c]["width"]=940;            
            $build_temp[$c]["height"]=ceil(568/$wide_ratio);
        }
        
        // find the right width of image - for efficiency
        if ($build_temp[$c]["width"]<=$item["mediumScale"])
            $build_temp[$c]["display_path"]=$image["mediumScalePath"];
        elseif ($build_temp[$c]["width"]>$item["mediumScale"]&&$build_temp[$c]["width"]<=$item["pageScale"])
            $build_temp[$c]["display_path"]=$image["pageScalePath"];
        else
            $build_temp[$c]["display_path"]=$image["largeScalePath"];
            
        // add to correct array
        if ($build_temp[$c]["width"]<=460)
            $portraits[]=$build_temp[$c];
        else
            $landscapes[]=$build_temp[$c];
            
        // increment stuff, including the widths of the image containing divs ready to slide in
        $wc+=940;
        $thumbw+=90;
        $c++;
    }
    
    //join landscapes to portraits to create one array, with markers for change
    array_splice($ips,0,0,$portraits);
    // step mark the array (1 step for each landscape, 1 step for two portraits)
    $ic=count($ips);
    $lc=count($landscapes);
    $num_ports=$ic; // for later
    $pc=0;
    for ($x=0;$x<$ic;$x++)
    {
        if ($x%2==0) $pc++;
        $ips[$x]["step"]=$pc+$lc;
    }
    for ($x=0;$x<$lc;$x++) $landscapes[$x]["step"]=$x+1;
    // splice the landscapes into the start of the array then we have our fully formatted array of images
    array_splice($ips,0,0,$landscapes);
    
    // dev_dump($ips);
    
    // set the thumbnail row width
    if (strlen($item["videoSRC"])) $thumbw+=90;
    if ($thumbw<940) $thumbw=940;
    
    // thumbnail row along the top of the page
    echo "<div id='fs_thumb_row'>";
    /* echo    "<div id='ivid_scroll_left' class='detail_page_link scroll_link left' onclick='fs_scroll(\"left\")'>&lt;";
    echo    "</div>"; */
    echo    "<div id='fsth_iefix'>";
    echo        "<div id='fs_thumb_window'>";
    echo            "<div id='fs_thumbnails' style='width:20000px'>"; /*".$thumbw."*/
    // create some blank thumbnails to fill in the row to the left
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    // output thumbs
    $tc=count($ips);
    $prev_step="";
    for ($x=0;$x<$tc;$x++)
    {
        if ($x<($tc-1)) $next_step=$ips[$x+1]["step"]; else $next_step=$ips[$x]["step"]+1;
        if ($x>0) $prev_step=$ips[$x-1]["step"]; else $prev_step=0;
        if ($ips[$x]["step"]==$prev_step)
        {
            // first we need to get the 'previous prev_step'
            if ($x>1) $prev_step=$ips[$x-2]["step"]; else $prev_step=0;
            echo "<img src='/".$ips[$x]["thumb"]."' class='fs_thumb' width='80' height='80' onclick='fs_shift(".$prev_step.",".$ips[$x-1]["step"].",".$ips[$x]["step"].",".$x.")'/>"; 
        }
        else
        {
            echo "<img src='/".$ips[$x]["thumb"]."' class='fs_thumb' width='80' height='80' onclick='fs_shift(".$prev_step.",".$ips[$x]["step"].",".$next_step.",".($x+1).")'/>";
        }
    }
    // more blank thumbs for the other side
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
    echo "<div class='blank_thumb'>";
    echo "</div>";
        
    echo            "</div>";
    echo        "</div>";
    echo    "</div>";
    /* echo    "<div id='ivid_scroll_right' class='detail_page_link scroll_link right' onclick='fs_scroll(\"right\")'>&gt;";
    echo    "</div>"; */
    // pulled up over the thumbnail row with margin so that it stays in the same place over the chosen thumbnail
    echo "<div id='fs_thumb_highlight'>";
    echo "</div>";
    echo "</div>";
    
    // output the images and the video, all in one div that will remain partly obscured ready to slide on click
    echo "<div id='fs_viewer_row'>";
    if (strlen($item["videoSRC"]))
    {
        $wc+=950; // extra width for video player
        echo "<div id='fs_viewer_window'>";
        echo    "<div id='fs_image_vid' style='width:".$wc."px'>";
        echo        "<div id='fs_vid'>";
        //1ST TRY - echo            video_player(array("item"=>$item,"width"=>"940","player_name"=>"fscreen"));
        
        /*2ND TRY - echo            "<a href='http://www.youtube.com/watch?v=".$item["videoSRC"]."' class='video-link'>Bolt Arms - Around the World</a>";
        echo open_script();
        echo            "$(document).ready(function() {";
        echo                "$('a.video-link').ytchromeless({ videoWidth:'940' , videoHeight:'568' , params:{ allowScriptAccess: 'always' , wmode: 'opaque' } });";
        echo            "});";
        echo close_script(); */
        
        echo "<div id='mediaplayer'>JW Player goes here</div>";       
        echo "<script type='text/javascript' src='/jwplayer/jwplayer.js'></script>";
        echo "<script type='text/javascript'>";
        echo    "jwplayer('mediaplayer').setup({";
        echo        "flashplayer: '/jwplayer/player.swf',";
        echo        "width: '940',";
        echo        "height: '553',";
        echo        "autostart: aplay,";
        echo        "backcolor: '000000',";
        echo        "frontcolor: 'de853e',";
        echo        "lightcolor: '68bfb7',";
        echo        "screencolor: '000000',";
        echo        "controlbar: 'bottom',";
        echo        "file: 'http://www.youtube.com/watch?v=".$item["videoSRC"]."',";
        echo        "image: '/img/preview.jpg'";
        echo    "});";
        echo "</script>";
        echo        "</div>";
    }
    else
    {
        echo "<div id='fs_viewer_window'>";
        echo    "<div id='fs_image_vid' style='width:".$wc."px'>";
    }
    // images, using above array
    $ic=count($ips);
    if (strlen($item["videoSRC"])) $start=1; else $start=0; // start one in because video already added to main image scroller
    for ($x=$start;$x<$ic;$x++)
        echo fs_image(array("image"=>$ips[$x],"count"=>$ic,"pcount"=>$num_ports,"curr"=>$x+1,"full_array"=>$ips));
    echo        "</div>";
    echo    "</div>";
    echo "</div>";
    
    
    /*echo "<div style='color:#fff'>";
    echo    "<span style='width:100%;float:left;'>thumb_curr: <span id='thumb_curr'></span></span>";
    echo    "<span style='width:100%;float:left;margin-bottom:20px;'>main_curr: <span id='main_curr'></span></span>";
    echo    "<span style='width:100%;float:left;'>clicked thumb: <span id='clicked_thumb'></span></span>";
    echo    "<span style='width:100%;float:left;'>clmain_prev: <span id='clmain_prev'></span></span>";
    echo    "<span style='width:100%;float:left;'>clmain_step: <span id='clmain_step'></span></span>";
    echo    "<span style='width:100%;float:left;margin-bottom:20px;'>clmain_next: <span id='clmain_next'></span></span>";
    echo    "<span style='width:100%;float:left;'>step diff thumb: <span id='step_thumb'></span>[<span id='thumb_dir'></span>]</span>";
    echo    "<span style='width:100%;float:left;margin-bottom:20px;'>step diff main: <span id='step_main'></span>[<span id='main_dir'></span>]</span>";
    echo    "<span style='width:100%;float:left;'>thumb_curr after: <span id='thumb_curr_after'></span></span>";
    echo    "<span style='width:100%;float:left;'>main_curr after: <span id='main_curr_after'></span></span>";
    echo "</div>";*/
    
    // close full screen and centre divs
    echo    "</div>";
    echo "</div>";
    
    /*echo "<div id='vid_de_out' style='background-color:#fff;color:#000;position:absolute;z-index:500000;top:800;left:200;'>";
    echo "<span class='vid_dev_text'>loaded (b): <span id='loaded'></span></span>";
    echo "<span class='vid_dev_text'>total (b): <span id='total'></span></span>";
    echo "<span class='vid_dev_text'>duration (s): <span id='duration'></span></span>";
    echo "<span class='vid_dev_text'>current (s): <span id='current_position'></span></span>";
    echo "<span class='vid_dev_text'>bar width: <span id='width'></span></span>";
    echo "<span class='vid_dev_text'>bytes per pixel: <span id='per_pixel'></span></span>";
    echo "<span class='vid_dev_text'>pixels covered: <span id='covered'></span></span>";
    echo "<span class='vid_dev_text'>secs per pixel: <span id='secs_pixel'></span></span>";
    echo "<span class='vid_dev_text'>pixels played: <span id='played_pixels'></span></span>";
    echo "<span class='vid_dev_text'>computed: <span id='computed'></span></span>";
    echo "</div>";*/
    mysql_data_seek($item_images,0);
?>