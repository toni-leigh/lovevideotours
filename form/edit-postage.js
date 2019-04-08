function save_threshold()
{
    var threshold=document.getElementById("p_thresh").value;
    if (isNaN(threshold))
    {
        
    }
    else
    {
        $.ajax({
          url: '/function/ajax/set_threshold.php',
          dataType: 'json',
          data: { threshold:threshold },
          success: function (new_html) { $("#postage_threshold").html(new_html);document.getElementById("p_thresh").value=threshold; }
        });
    }
}
function add_bracket(calc_type,last_max)
{
    var elem = document.getElementById(calc_type).elements;
    var value_pass=1;
    var new_count=0;
    var total_count=0;
    for (var i = 0; i < elem.length; i++)
    {
        if ((isNaN(elem[i].value)||elem[i].value=="")&&elem[i].value!="MAX")
        {
            total_count++;
            $("#"+elem[i].name).removeClass('text_field');
            $("#"+elem[i].name).addClass('text_field_bad');
            value_pass=0;
            if (elem[i].name.indexOf("new",0)>=0)
                new_count++;
        }
        if (new_count==3)
        {
            $("#new_"+calc_type+"_min_value, #new_"+calc_type+"_max_value, #new_"+calc_type+"_cost").removeClass('text_field_bad').addClass('text_field');
        }
    }
    if (new_count==3&&total_count==3)
        value_pass=1;
    if (value_pass==1)
    {
        var inputs=$("#"+calc_type).serialize();
        $.ajax({
          url: '/function/ajax/add_bracket.php',
          dataType: 'json',
          data: { inputs:inputs , calc_type:calc_type },
          success: function (new_html) { $("#"+calc_type+"_brackets").html(new_html); }
        });
    }
}
function remove_postage(calc_type,charge_ID)
{
    $.ajax({
      url: '/function/ajax/remove_bracket.php',
      dataType: 'json',
      data: { charge_ID:charge_ID , calc_type:calc_type },
      success: function (new_html) { $("#"+calc_type+"_brackets").html(new_html); }
    });
}