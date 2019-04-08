$('#facebook_login').click(function(){
    FB.login(function(response)
    {
        if (response.session)
        {
            if (response.perms)
            {
                top.location.href='/facebook_connect.php';
            }
            else
            {
                // user is logged in, but did not grant any permissions
            }
        }
        else
        {
            // user is not logged in
        }
    },
    {perms:'email,user_location,user_birthday,user_likes'});
    return false;
});

function home_vid(direction,current_id,count,next_load_id,next_load_src)
{
    var move_amount;
    if (direction=='right')
    {
        // only slide
        move_amount="-=940";
        // set up next vid and slide
        if ((count-current_id)>2)
        {
            jwplayer('media_player'+next_load_id).setup
            ({
                flashplayer: '/jwplayer/player.swf',
                width: '940',
                height: '553',
                autostart: 'false',
                backcolor: '000000',
                frontcolor: 'de853e',
                lightcolor: '68bfb7',
                screencolor: '000000',
                controlbar: 'bottom',
                file: 'http://www.youtube.com/watch?v='+next_load_src,
                image: '/img/preview.jpg'
            });
        }
    }
    else
    {
        move_amount="+=940";
        
        // check if the div has a vid already
        
        // then setup
        
        // else just slide
    }
    $("#fs_image_vid").animate({ left:move_amount },400);
}


function show_engage_form(form_button)
{
    document.getElementById("engage_hidden_field").innerHTML="<input type='hidden' name='engage_type' value='"+form_button+"'>";
    document.getElementById("engage_form_display").style.display="block";
    document.getElementById("engage_submit_button").innerHTML="<input id='"+form_button+"_submit' class='submit_button button' name='"+form_button+"_submit' type='submit' value='"+form_button+"'>";
    document.engage_form.email.focus();
}
function hide_engage()
{
    document.getElementById("engage_form_display").style.display="none";
}
/*these two functions are global to allow anything to be added to the basket from anywhere on the site*/
function updateBag(item_ID,user_ID)
{
    variation_ID=document.getElementById("item_variation"+item_ID).value;
    quantity=document.getElementById("quantity"+item_ID).value;
    $.ajax({
      url: '/function/ajax/update_bag.php',
      dataType: 'json',
      data: { item_ID: item_ID, variation_ID: variation_ID, quantity:quantity, user_ID:user_ID },
      success: function (new_html) { $("#basket_panel").html(new_html); }
    });
}
function saveComment(entityID,entityType,entitySubType)
{
    latestCommentsContent=document.getElementById("latest_comments").innerHTML;
    commentToSave=document.getElementById("add_comment_box").value;
    if (commentToSave!="")
    {
        $.ajax({
          url: '/function/ajax/save_comment.php',
          dataType: 'json',
          data: { entityID:entityID, entityType:entityType, entitySubType:entitySubType, latestCommentsContent:latestCommentsContent, commentToSave:commentToSave },
          success: function (new_html) { $("#latest_comments").html(new_html); $("#no_comments_yet").html=""; }
        });
    }
}
function recordBasicSocialAction(entityID,entityType,entitySubType,actionType,recommendState)
{
    $.ajax({
      url: '/function/ajax/basic_social_action.php',
      dataType: 'json',
      data: { entityID: entityID, entityType: entityType, entitySubType:entitySubType, actionType:actionType, recommendState:recommendState },
      success: function (new_html) { $("#social_button_panel").html(new_html); }
    });
}