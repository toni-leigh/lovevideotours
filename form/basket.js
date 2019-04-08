function checkVoucherCode(order_total,product_total,postage_total)
{
  var voucher_code=document.getElementById("voucher_field").value;
  $.ajax({
    url:'/function/ajax/check_voucher_code.php',
    dataType:'json',
    data:{voucher_code:voucher_code,order_total:order_total,product_total:product_total,postage_total:postage_total},
    success:updateVoucherOutput
  });
}
function updateVoucherOutput(new_html)
{
  $("#basket_total").html(new_html[0]);
  $("#voucher_message").html(new_html[3]);
}