function set_supplier_panel(new_panel)
{
    var html="";
    if (new_panel=="intro") html=introHTML;
    else if (new_panel=="products") html=productHTML;
    else if (new_panel=="blog") html=blogHTML;
    else if (new_panel=="activity") html=activityHTML;
    else if (new_panel=="postage") html=postageHTML;
    document.getElementById("supplier_content").innerHTML=html;
}