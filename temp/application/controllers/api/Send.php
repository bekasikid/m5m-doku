<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';


    //@TODO : tanyain SESSIONID itu apa
    //@TODO : dimana menetukan metode bayar

class Send extends REST_Controller {
    public $mallid = 2864;
    public $key = "w45w9nX8dLAM";

    function __construct()
    {

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
            "BASKET" =>  $this->post("basket"),
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

}
