<?php

$order_number = $_POST['TRANSIDMERCHANT'];
$purchase_amt = $_POST['AMOUNT'];
$status_code = $_POST['STATUSCODE'];
$words = $_POST['WORDS'];
$paymentchannel = $_POST['PAYMENTCHANNEL'];
$session_id = $_POST['SESSIONID'];
$paymentcode = $_POST['PAYMENTCODE'];

$dokuform="<form name=\"param_pass\" id=\"param_pass\" method=\"post\" action=\"http://http://www.domainanda.com/directory/result.php\">\n"; //example redirect link

$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$order_number\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$purchase_amt\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$status_code\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$words\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$paymentchannel\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$session_id\">\n";
$dokuform.="<input name=\"order_number\" type=\"hidden\" id=\"order_number\" value=\"$paymentcode\">\n";

$dokuform.="</form>\n";
$dokuform.="<script language=\"JavaScript\" type=\"text/javascript\">";
$dokuform.="document.getElementById('param_pass').submit();";
$dokuform.="</script>";

//FIREFOX FIX
$redirect_url = str_replace('&amp;', '&', $redirect_url);

?>
<body>
<? print $dokuform; ?>
<noscript>
If you are not redirected please <a href="<?php echo $redirect_url; ?>">click here</a> to confirm your order.
</noscript>
</body>