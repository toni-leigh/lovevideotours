function toggle_fcheck(check_ID)
{
    if ($("#"+check_ID+"feature").attr("checked"))
    {
        $("#"+check_ID+"feature").attr("checked", true);
        $("#"+check_ID+"f").removeClass("unchecked").addClass("checked");
    }
    else
    {
        $("#"+check_ID+"feature").attr("checked", false);
        $("#"+check_ID+"f").removeClass("checked").addClass("unchecked");
    }
}