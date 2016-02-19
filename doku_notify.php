<?php
$db = "testing_doku";
$connect = mysql_connect("localhost", "testing_doku", "pwddoku") or die("error connection");
mysql_select_db("$db", $connect) or die ("error select DB");

//$ip_range = "103.10.129.";

//if ( $_SERVER['REMOTE_ADDR'] != '182.253.5.91' && (substr($_SERVER['REMOTE_ADDR'],0,strlen($ip_range)) !== $ip_range) )
{


    if ($_POST['TRANSIDMERCHANT']) {
        $order_number = $_POST['TRANSIDMERCHANT'];

    } else {
        $order_number = 0;
    }
    $totalamount = $_POST['AMOUNT'];
    $words = $_POST['WORDS'];
    $statustype = $_POST['STATUSTYPE'];
    $response_code = $_POST['RESPONSECODE'];
    $approvalcode = $_POST['APPROVALCODE'];
    $status = $_POST['RESULTMSG'];
    $paymentchannel = $_POST['PAYMENTCHANNEL'];
    $paymentcode = $_POST['PAYMENTCODE'];
    $session_id = $_POST['SESSIONID'];
    $bank_issuer = $_POST['BANK'];
    $cardnumber = $_POST['MCN'];
    $payment_date_time = $_POST['PAYMENTDATETIME'];
    $verifyid = $_POST['VERIFYID'];
    $verifyscore = $_POST['VERIFYSCORE'];
    $verifystatus = $_POST['VERIFYSTATUS'];

// Basic SQL

    $sql = "select transidmerchant,totalamount from doku where transidmerchant='" . $order_number . "'and trxstatus='Requested'";
    $checkout = mysql_fetch_array(mysql_query($sql));
    // echo "sql : ".$sql;
    $hasil = $checkout['transidmerchant'];
    $amount = $checkout['totalamount'];


// Custom Field

    if (!$hasil) {

        echo 'Stop1';

    } else {

        if ($status == "SUCCESS") {
            $sql = "UPDATE doku SET trxstatus='Success', words='$words', statustype='$statustype', response_code='$response_code', approvalcode='$approvalcode',
		         trxstatus='$status', payment_channel='$paymentchannel', paymentcode='$paymentcode', session_id='$session_id', bank_issuer='$bank_issuer', creditcard='$cardnumber',
			 payment_date_time='$payment_date_time', verifyid='$verifyid', verifyscore='$verifyscore', verifystatus='$verifystatus' where transidmerchant='$order_number'";
            // echo "sql : ".$sql;
            $result = mysql_query($sql) or die ("Stop2");

        } else {

            $sql = "UPDATE doku set trxstatus='Failed' where transidmerchant='" . $order_number . "'";

            $result = mysql_query($sql) or die ("Stop3");


        }
        echo 'Continue';

    }

    mysql_close();
}
?>