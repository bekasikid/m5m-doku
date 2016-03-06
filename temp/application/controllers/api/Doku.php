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

        $waktu = time();

        $regs = $this->db->where("registration_code",$this->post("id"))->get("registrations");

        if($regs->num_rows()==1){
            $reg = $regs->row_array();

            $payMets = $this->db->where("doku_id",$this->post("method"))->get("payment_method");
            $payMet = $payMets->row_array();

            $kata = $payMet["method_price"].".00".$this->mallid.$this->key.$reg["registration_id"];
            $words = sha1($kata);

            $this->db->insert("doku",array(
                "registration_code" => $this->post("id"),
                "transidmerchant" => $reg["registration_id"],
                "totalamount" => $payMet["method_price"],
                "words"=> $words,
                "trxstatus"=>"Requested",
                "payment_channel"=> $this->post("method"),
                "session_id" => $this->post("id"),
                "payment_date_time" => date("Y-m-d H:i:s",$waktu),
            ));



            $array = array(
                "MALLID" => $this->mallid,
                "CHAINMERCHANT" => 0,
                "AMOUNT" => $payMet["method_price"],
                "PURCHASEAMOUNT" => $payMet["method_price"],
                "TRANSIDMERCHANT" => $reg["registration_id"],
                "WORDS" => $words,
                "kata" => $kata,
                "REQUESTDATETIME" => date("YmdHis",time()),
                "CURRENCY" => 360,
                "PURCHASECURRENCY" => 360,
                "SESSIONID" => $this->post("id"),
                "NAME" => "",
                "EMAIL" => "",
                "BASKET" =>  $payMet['method_desc'],
                "PAYMENTCHANNEL" => $payMet['doku_id']
            );
        }
        $row = array(
            "code" => 200,
            "message" => "success",
            "data" => $array
        );
        $this->response($row,200);
    }

    public function notifikasi_post(){
        file_put_contents("./logs/iseng.txt",json_encode($_POST),FILE_APPEND);
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
            $rows = $this->db->where("transidmerchant",$order_number)->where("trxstatus","Requested")->from("doku")->get();
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

    public function redirect_post(){
        $order_number = $this->post('TRANSIDMERCHANT');
        $purchase_amt = $this->post('AMOUNT');
        $status_code = $this->post('STATUSCODE');
        $words = $this->post('WORDS');
        $paymentchannel = $this->post('PAYMENTCHANNEL');
        $session_id = $this->post('SESSIONID');//kode register
        $paymentcode = $this->post('PAYMENTCODE');
        redirect("http://doku.menang5miliar.com/bayar/#/home/doku".$session_id);
    }

    public function confirm_get(){
        $data = $this->db->where("registration_code",$this->get("id"))->get("doku");
        $row = array(
            "code" => 200,
            "message" => "success",
            "data" => $data->row_array()
        );
        $this->response($row,200);
    }

    public function iseng_get(){
        echo "Stop";
    }

}
