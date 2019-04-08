function checkVoucherCode(order_total,product_total,postage_total)
{
  var voucher_code=document.getElementById("voucher-code").value;
  $.ajax({
    url:'/function/ajax/check_voucher_code.php',
    dataType:'json',
    data:{voucher_code:voucher_code,order_total:order_total,product_total:product_total,postage_total:postage_total},
    success:updateVoucherOutput
  });
}
function updateVoucherOutput(new_html)
{
  $("#order-total-display").html(new_html[0]);
  $("#voucher-message").html(new_html[3]);
}