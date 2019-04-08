function clear_field(input)
{
    //resets a field to default, empty, unhighlighted, unerrored
    document.getElementById(input).style.border='2px #808080 solid';
    document.getElementById(input+"_message").innerHTML=""; 
    document.getElementById(input+"_header").style.color="#000";
}
function set_field_passed(input)
{
    //sets a field as passed, filled, unhighlighted, unerrored
    document.getElementById(input).style.border='2px #808080 solid';
    document.getElementById(input+"_message").innerHTML="";
    document.getElementById(input+"_header").style.color="#000";
}
function set_field_failed(input,message)
{
    //sets a field as failed, filled, highlighted, errored
    document.getElementById(input).style.border='2px #fec111 solid';
    document.getElementById(input+"_message").innerHTML=message;    
    document.getElementById(input+"_header").style.color="#fec111";
}
function check_email_input()
{
    var email = document.getElementById('primary_email');
    var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email.value))
    {
        set_field_failed("primary_email","Invalid Email");           
    }
    else
    {
        set_field_passed("primary_email");
    }
}
function populate_address()
{
    var delivery_array=["delivery_first_name","delivery_last_name","delivery_street1","delivery_street2","delivery_city","delivery_postal_code"];
    var billing_array=["billing_first_name","billing_last_name","billing_street1","billing_street2","billing_city","billing_postal_code"];
    //check the state of the checkbox, if clicked to the same we want to fill the fields and deactivate, if not we want to activate and blank the fields
    if (document.address_form.delivery_copy_checkbox.checked==true)
    {
        //basic address fields
        for (i=0;i<6;i++)
        {
            document.getElementById(billing_array[i]).value=document.getElementById(delivery_array[i]).value;
            /*if (document.getElementById(delivery_array[i]).value=="") {set_field_failed(delivery_array[i]," must be filled");}
            else {set_field_passed(delivery_array[i]);}
            if (document.getElementById(billing_array[i]).value=="") {set_field_failed(billing_array[i]," must be filled");}
            else {set_field_passed(billing_array[i]);}*/
        }
    }
    else
    {
        for (i=0;i<6;i++)
        {
            clear_field(billing_array[i]);
            document.getElementById(billing_array[i]).value="";
        }
    }
}
function copy_delivery(copy_from,copy_to)
{
    document.getElementById(copy_to).value=document.getElementById(copy_from).value;
}
function check_text_input(input,name)
{
    if (document.getElementById(input).value=='')
    {
        set_field_failed(input," must be filled");
    }
    else
    {
        set_field_passed(input);
    }
}
function billing_state(current_state)
{
    if (document.getElementById("billing_country_code").value=="US")
    {
        $.ajax({
          url: '/function/ajax/build_state_dropdown.php',
          dataType: 'json',
          data: {current_state:current_state},
          success: updateAddressForm
        });
    }
    else
    {
        document.getElementById("billing_state_marker").innerHTML="";
    }
}
function updateAddressForm(html)
{
    document.getElementById("billing_state_marker").innerHTML=html;
}