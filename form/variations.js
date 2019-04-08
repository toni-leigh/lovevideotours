function set_vtype_output(vtype_ID)
{
    if ($("#"+vtype_ID+"_vtype").attr("checked"))
    {
        $("."+vtype_ID+"vtype").removeClass("hidden");
        new_setting=0;
    }
    else
    {
        $("."+vtype_ID+"vtype").addClass("hidden");
        new_setting=1;
    }
    var inputs=$("#variation_adder").serialize();
    $.ajax({
      url: '/function/ajax/save_pvariations.php',
      dataType: 'json',
      data: { vtype_ID:vtype_ID , new_setting:new_setting , inputs:inputs },
      success: function (new_html) { $("#variation_preview").html(new_html); }
    }); 
}
function update_output_panel()
{
    var inputs=$("#variation_adder").serialize();
    $.ajax({
        type: 'POST',
        url: '/function/ajax/update_vpanel.php',
        dataType: 'json',
        data: { inputs:inputs },
        success: function (new_html) { $("#variation_preview").html(new_html); }
    });
    
    // loop over adder inputs serialised above
    
    // for each check for text adder
    
    // if text adder call function update_preview_text() with right pvtype ID
}
function show_message()
{
    document.getElementById("changes_made").style.display="block";
}
function mark_row(variation_ID)
{
    show_message();
    // remove the current line
    remove_lines(variation_ID);
    
    // perform checks to find out what the row should display, if depth reflects lack of priority
    if ($("."+variation_ID+"remove").attr("checked"))
        $("#"+variation_ID+"variation").addClass("remove_row");
    else
        if ($("."+variation_ID+"instock").attr("checked"))
            if ($("."+variation_ID+"main").attr("checked"))
                $("#"+variation_ID+"variation").addClass("main_row");
            else
                $("#"+variation_ID+"variation").addClass("instock_row");
        else
            if ($("."+variation_ID+"main").attr("checked"))
                $("#"+variation_ID+"variation").addClass("main_row_out");
            else
                $("#"+variation_ID+"variation").addClass("outstock_row");
}
function mark_main(variation_ID,last_main)
{
    show_message();
    // get the remove checkboxes displayed right
    if ($(".remove_checkbox").hasClass("hidden")) $(".remove_checkbox").removeClass("hidden");
    $("."+variation_ID+"remove").addClass("hidden");
    $("."+variation_ID+"remove").attr("checked",false);
    
    // remove the old variation
    remove_lines(main_ID);
    mark_row(main_ID);
    
    // then mark the row
    mark_row(variation_ID);
    
    // set main ready for next mark
    main_ID=variation_ID;
}
function remove_lines(variation_ID)
{
    $("#"+variation_ID+"variation").removeClass("outstock_row").removeClass("instock_row").removeClass("main_row").removeClass("main_row_out").removeClass("remove_row");
}
function add_variations(price_ID,post_ID)
{
    var new_vars="";
    $('.add_these').each(function(index) {
        new_vars+=$(this).val()+"|";
    });
    price_value=$('#'+price_ID+'value_add').val();
    post_value=$('#'+post_ID+'value_add').val();
    $.ajax({
        type: 'POST',
        url: '/function/ajax/save_variations.php',
        dataType: 'json',
        data: { new_vars:new_vars , price_value:price_value , post_value:post_value },
        success: function (new_html) { $("#variation_panel").html(new_html[0]);$("#feedback_message").html(new_html[1]); }
    });
}
function save_variations()
{
    var inputs=$("#variation_editor").serialize();
    $.ajax({
        type: 'POST',
        url: '/function/ajax/update_variations.php',
        dataType: 'json',
        data: { inputs:inputs },
        success: function (new_html) { $("#variation_panel").html(new_html[0]); $("#feedback_message").html(new_html[1]); document.getElementById("changes_made").style.display="none"; }
    });
}
function update_preview_text(pvtype_ID,pvtype_name)
{
    var new_val=document.getElementById(pvtype_ID+"value_add").value;
    if (pvtype_name=="price") new_val="£"+formatNumber(new_val,2,' ','.','','','-','');
    $("."+pvtype_ID+"pr_val_pvtype").html(new_val);
}
function formatNumber(num,dec,thou,pnt,curr1,curr2,n1,n2) {var x = Math.round(num * Math.pow(10,dec));if (x >= 0) n1=n2='';var y = (''+Math.abs(x)).split('');var z = y.length - dec; if (z<0) z--; for(var i = z; i < 0; i++) y.unshift('0'); if (z<0) z = 1; y.splice(z, 0, pnt); if(y[0] == pnt) y.unshift('0'); while (z > 3) {z-=3; y.splice(z,0,thou);}var r = curr1+n1+y.join('')+n2+curr2;return r;}
function update_price_output()
{
    
}
function update_postage_output()
{
    
}
function set_master_price()
{
    var price_value=document.getElementById("master_price").value;
    if (isNaN(price_value)||price_value=="")
    {
        $("#master_price").removeClass('text_field').addClass('text_field_bad');
        $('#use_master_price').attr('checked',false);
    }
    else
    {
        $("#master_price").removeClass('text_field_bad').addClass('text_field');
        if (!$('#use_master_price').attr('checked')) price_value=0;
        $.ajax({
          url: '/function/ajax/set_master_price.php',
          dataType: 'json',
          data: { price_value:price_value },
          success: function (new_html) { $("#master_variation_panel").html(new_html); }
        }); 
    }
}
function set_master_postage()
{
    var postage_value=document.getElementById("master_postage").value;
    if (isNaN(postage_value)||postage_value=="")
    {
        $("#master_postage").removeClass('text_field').addClass('text_field_bad');
        $('#use_master_postage').attr('checked',false);
    }
    else
    {
        $("#master_postage").removeClass('text_field_bad').addClass('text_field');
        if (!$('#use_master_postage').attr('checked')) postage_value=0;
        $.ajax({
          url: '/function/ajax/set_master_postage.php',
          dataType: 'json',
          data: { postage_value:postage_value },
          success: function (new_html) { $("#master_variation_panel").html(new_html); }
        }); 
    }
}