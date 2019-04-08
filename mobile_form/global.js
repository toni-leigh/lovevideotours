if (window.focus)
{
    if (document.getElementById("header_facebook"))
    {
        document.getElementById("header_facebook").innerHTML="<span id='facebook_login'>facebook</span>";
    }
    if (document.getElementById("comment_box"))
    {
        document.getElementById("comment_box").innerHTML=comment_box_html;
    } 
}
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
function engage_form(form_button)
{
    document.getElementById("engage_form_display").style.display="block";
    document.getElementById("engage_submit_button").innerHTML="<input id='"+form_button+"_submit' class='submit_button button' name='"+form_button+"_submit' type='submit' value='"+form_button+"'>";
    document.engage_form.email.focus();
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
    $.ajax({
      url: '/function/ajax/save_comment.php',
      dataType: 'json',
      data: { entityID:entityID, entityType:entityType, entitySubType:entitySubType, latestCommentsContent:latestCommentsContent, commentToSave:commentToSave },
      success: function (new_html) { $("#latest_comments").html(new_html); }
    });
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