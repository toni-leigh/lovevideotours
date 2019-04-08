var current_position=0;
var cued=0;
    

var thumb_curr=1; // set to current in gallery, globally
var main_curr=1;
function onYouTubePlayerReady(playerId)
{
    ytplayer1 = document.getElementById("lvplayerDiv");
    ytplayer2 = document.getElementById("fscreenDiv");
    if (playerId=="lvplayer")
    {
        setInterval(updatelvtplayerInfo, 200);
        updatelvtplayerInfo();
    }
    else
    {
        setInterval(updatefscreenInfo, 200);
        updatefscreenInfo();
    }
    document.getElementById("control_bar").style.display="block";
    document.getElementById("fscreenplay_button").style.display="block";
    // $("#fscreenplay_button").css("z-index","1000000");
    if (autoplay==1)
        play_video(autoplayVid,"fscreen");
    
    if (typeof("ie7_play")!="undefined"&&document.getElementById("ie7_play")!=null) document.getElementById("ie7_play").style.display="block"; 
        
    ytplayer1.addEventListener("onStateChange", "onytplayerStateChange");
    ytplayer1.addEventListener("onError", "onPlayerError");
    ytplayer2.addEventListener("onStateChange", "onytplayerStateChange");
    ytplayer2.addEventListener("onError", "onPlayerError");
    
}
function updatelvtplayerInfo()
{
    update("lvplayer");
}
function updatefscreenInfo()
{
    update("fscreen");
}
function update(playerId)
{
    var bytes_loaded=getBytesLoaded(playerId);
    var total_bytes=getBytesTotal(playerId);
    var bar_width=$("#"+playerId+"seek_bar").width();
    var bytes_per_pixel=total_bytes/bar_width;
    var pixels_covered=Math.ceil(bytes_loaded/bytes_per_pixel);
    
    var duration=getDuration(playerId);
    var current=getCurrentTime(playerId);
    var secs_per_pixel=duration/bar_width;
    var played_pixels=Math.ceil(current/secs_per_pixel);
    
    var played_bar=played_pixels-8;
    var extra_pixels_for_loaded=pixels_covered-played_pixels;
    if (extra_pixels_for_loaded<0) extra_pixels_for_loaded=0;
        
    /*updateHTML("loaded", bytes_loaded);
    updateHTML("total", total_bytes);
    updateHTML("width", bar_width);
    updateHTML("per_pixel", bytes_per_pixel);
    updateHTML("covered", pixels_covered);
    
    updateHTML("duration", duration);
    updateHTML("current_position", current);
    updateHTML("secs_pixel", secs_per_pixel);
    updateHTML("played_pixels", played_pixels);
    updateHTML("computed", extra_pixels_for_loaded);*/    
    
    // display loaded on bar - note, all the extra stuff is to ensure this works in older ies, so leave it ! ugh :-< !!!
    if (isNaN(extra_pixels_for_loaded)) var loaded_pix="0px"; else var loaded_pix=extra_pixels_for_loaded+"px";
    if (isNaN(played_bar))
    {
        var played_pix="0px";
        var played_bar_jq=0;
    }
    else
    {
        var played_pix=played_bar+"px";
        var played_bar_jq=played_bar;
    }
    document.getElementById("fscreenloaded_bar").style.width=loaded_pix;  
    // document.getElementById("fscreenplayed_bar").style.width=played_pix;
    $("#fscreenplayed_bar").css("width",played_bar_jq);
}
function updateHTML(id,value)
{
    document.getElementById(id).innerHTML=value;
}

function getBytesLoaded(playerId){ if (fscreen) return fscreen.getVideoBytesLoaded(); }
function getBytesTotal(playerId){ if (fscreen) return fscreen.getVideoBytesTotal(); }
function getCurrentTime(playerId){ if (fscreen) return fscreen.getCurrentTime(); }
function getDuration(playerId){ if (fscreen) return fscreen.getDuration();}

function play_video(videoID,playerID)
{
    document.getElementById(playerID+"play_button").style.display="none";
    if (cued==0)
    {
        fscreen.cueVideoById(videoID,current_position,'default');
        cued=1;
    }
    if (fscreen.getPlayerState()==1)
    {
        fscreen.pauseVideo();
        if (typeof("ie7_play")!="undefined"&&document.getElementById("ie7_play")!=null) document.getElementById("ie7_play").innerHTML="click here to play video";
    }
    else
    {
        fscreen.playVideo();
        if (typeof("ie7_play")!="undefined"&&document.getElementById("ie7_play")!=null) document.getElementById("ie7_play").innerHTML="click here to pause video";
    }
}
function pause_video(playerID)                                          
{
    current_position=getCurrentTime(playerID);
    if (fscreen.getPlayerState()==1)
    {
        document.getElementById(playerID+"play_button").style.display="block";
        fscreen.pauseVideo();
        if (typeof("ie7_play")!="undefined"&&document.getElementById("ie7_play")!=null) document.getElementById("ie7_play").innerHTML="click here to play video";
    }
    else
    {
        document.getElementById(playerID+"play_button").style.display="none";
        fscreen.playVideo();
        if (typeof("ie7_play")!="undefined"&&document.getElementById("ie7_play")!=null) document.getElementById("ie7_play").innerHTML="click here to pause video";
    }
}

/* image gallery functions */
function scroll(direction)
{
    var move_amount;
    if (direction=="left") move_amount="+=217"; else move_amount="-=217";
    $("#all_thumbnails").animate({ left:move_amount },400);
}
// scrolls through 1 - many thumbs to find the one clicked on by user, in are the clicked step and next step values
function fs_shift(clmain_prev,clmain_step,clmain_next,clthumb_step)
{    
    var thumb_amount=0;
    var main_amount=0;
    
    if (typeof(fscreen)!="undefined"&&clmain_step!=0) if (fscreen.getPlayerState()==1) pause_video("fscreen");
    
    // change border size if needed
    if (clmain_step==clmain_next||clmain_step==clmain_prev)
        $("#fs_thumb_highlight").animate({ width:"166px" },600);
    else
        $("#fs_thumb_highlight").animate({ width:"76px" },600);
        
    // set thumb shift
    step_diff=Math.abs(clthumb_step-thumb_curr);
    if (clthumb_step<thumb_curr) thumb_amount="+="+(step_diff*90); else thumb_amount="-="+(step_diff*90);
    // set main shift
    step_diff=Math.abs(clmain_step-main_curr);   
    if (clmain_step<main_curr) main_amount="+="+(step_diff*940); else main_amount="-="+(step_diff*940);
        
    // perform animation
    $("#fs_thumbnails").animate({ left:thumb_amount },100);
    $("#fs_image_vid").animate({ left:main_amount },200);
    
    // set the current thumb step
    thumb_curr=clthumb_step;
    main_curr=clmain_step;
}
function fs_show()
{
    $("#full_screen").css("display","block");
}
function close_fs()
{
    pause_video("fscreen");
    $("#full_screen").css("display","none");
}

/* map and filter */
function toggle_check(check_ID,curr_cat)
{
    if ($("#"+check_ID).attr("checked"))
    {
        $("#"+check_ID).attr("checked", false);
    }
    else
    {
        $("#"+check_ID).attr("checked", true);
    }
    var checks=$("#fcat_form").serialize();
    set_map_items(checks);
    // only ajax the list if not holiday property list
    if (curr_cat!=3)
    {
        $.ajax({
          url: '/function/ajax/filter_vitems.php',
          dataType: 'json',
          data: { checks:checks },
          success: function (new_html) { $("#vitem_list").html(new_html); }
        }); 
    }
}
function set_map_items(checks)
{
    // alert(checks);
    for (x in markers)
    {
        if (typeof(markers[x][0])!="undefined")
        {
            if (markers[x][1]!=3) // always leave the holiday property on the map
            {
                // alert(checks.indexOf(markers[x][1],0));
                if (checks.indexOf(markers[x][1],0)>=0)
                {
                    // if (markers[x][1]==9) alert("pyeb");
                    if (markers[x][0].getMap()==null)
                    {
                        markers[x][0].setMap(map);
                        markers[x][0].setZIndex(1000);
                    }
                    else
                    {
                        if (markers[x][0].getZIndex()==1000)
                        {
                            markers[x][0].setZIndex(10);
                        }
                    }
                }
                else
                {
                    markers[x][0].setMap(null);
                }
            }
        }
    }
}