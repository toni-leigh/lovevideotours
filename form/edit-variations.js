function save_new_vvalue(vtype_ID)
{
    var vvalue=document.getElementById(vtype_ID+"variation_value").value;
    if (vvalue!="")
    {
        $.ajax({
          url: '/function/ajax/save_variation_value.php',
          dataType: 'json',
          data: { vtype_ID:vtype_ID , vvalue:vvalue },
          success: function (new_html) { $("#vtype_values"+vtype_ID).html(new_html); document.getElementById(vtype_ID+"variation_value").value=""; }
        });
    }
}
function remove_vvalue(vtype_ID,vvalue_ID)
{
    $.ajax({
      url: '/function/ajax/remove_variation_value.php',
      dataType: 'json',
      data: { vtype_ID:vtype_ID , vvalue_ID:vvalue_ID },
      success: function (new_html) { $("#vtype_values"+vtype_ID).html(new_html); }
    });
}