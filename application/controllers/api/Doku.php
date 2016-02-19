<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
    doku ip : 103.10.129.0 - 103.10.129.24
 */
class Doku extends REST_Controller {
    public $mallid = 2864;
    public $key = "w45w9nX8dLAM";

    function __construct()
    {
        parent::__construct();
    }

    public function payment_post(){

        $array = array(
            "MALLID" => $this->mallid,
            "CHAINMERCHANT" => 0,
            "AMOUNT" => $this->post('amount'),
            "PURCHASEAMOUNT" => $this->post('amount'),
            "TRANSIDMERCHANT" => $this->post('id'),
            "WORDS" => sha1($this->post('amount').$this->mallid.$this->key,$this->post('id')),
            "REQUESTDATETIME" => date("YmdHis"),
            "CURRENCY" => 360,
            "PURCHASECURRENCY" => 360,
            "SESSIONID" => "ini mau diisi apa?",
            "NAME" => $this->post("name"),
            "EMAIL" => $this->post("email"),
//            "BASKET" =>  $this->post("basket"),
            "BASKET" =>  "⁠⁠⁠Paket Balap Makan Ayam (PBA),150000.00,1,150000.00",
            "PAYMENTCHANNEL" => $this->post("channel")
        );
        $this->db->insert("doku",array(
            "transidmerchant" => $this->post("id"),
            "totalamount" => $this->post("amount"),
            "words"=>$array['WORDS'],
            "trxstatus"=>"Requested",
            "payment_channel"=> $this->post("channel"),
            "session_id" => $array['SESSIONID'],
            "payment_date_time" => $array['REQUESTDATETIME'],
        ));
    }

    public function notify_post(){

        if ($this->post('TRANSIDMERCHANT')) {
            $order_number = $this->post('TRANSIDMERCHANT');
        }
        else {
            $order_number = 0;
        }
        $totalamount = $this->post('AMOUNT');
        $words    = $this->post('WORDS');
        $statustype = $this->post('STATUSTYPE');
        $response_code = $this->post('RESPONSECODE');
        $approvalcode   = $this->post('APPROVALCODE');
        $status         = $this->post('RESULTMSG');
        $paymentchannel = $this->post('PAYMENTCHANNEL');
        $paymentcode = $this->post('PAYMENTCODE');
        $session_id = $this->post('SESSIONID');
        $bank_issuer = $this->post('BANK');
        $cardnumber = $this->post('MCN');
        $payment_date_time = $this->post('PAYMENTDATETIME');
        $verifyid = $this->post('VERIFYID');
        $verifyscore = $this->post('VERIFYSCORE');
        $verifystatus = $this->post('VERIFYSTATUS');

        if($order_number){
            $rows = $this->db->where("transidmerchant",$order_number)->where("trxstatus","Requested")->from("doku");
            if($rows->num_rows()==1){
                $row = $rows->row_array();
                $hasil=$row['transidmerchant'];
                $amount=$row['totalamount'];
                if ($status=="SUCCESS") {
                    $this->db->where("transidmerchant",$order_number)->update("doku",array(
                        "trxstatus"=>'Success',
                        "words" => $words,
                        "statustype" => $statustype,
                        "response_code" => $response_code,
                        "approvalcode" => $approvalcode,
                        "trxstatus" => $status,
                        "payment_channel" => $paymentchannel,
                        "paymentcode" => $paymentcode,
                        "session_id" => $session_id,
                        "bank_issuer" => $bank_issuer,
                        "creditcard" => $cardnumber,
                        "payment_date_time" => $payment_date_time,
                        "verifyid" => $verifyid,
                        "verifyscore" => $verifyscore,
                        "verifystatus" => $verifystatus
                    ));
                    if($this->db->affected_rows()){
                        echo "continue";
                    }else{
                        echo "Stop2";
                    }

                } else {
                    $this->db->where("transidmerchant",$order_number)->update("doku",array(
                        "trxstatus"=>'Failed'
                    ));
                    echo "Stop3";
                }
            }else{
                echo "Stop1";
            }
        }else{
            echo "Stop1";
        }

    }

    public function iseng_get(){
        echo "Stop";
    }

}
