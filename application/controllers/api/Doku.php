<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
    doku ip : 103.10.129.0 - 103.10.129.24
 */
class Doku extends REST_Controller {
    //dev
//    public $mallid = 2864;
//    public $key = "w45w9nX8dLAM";

    //prod
    public $mallid = 903;
    public $key = "JVrCZkXH0urp";

    function __construct()
    {
        parent::__construct();
    }

    public function payment_post(){

        $waktu = time();
        $row = array();
        $regs = $this->db->where("registration_code",$this->post("id"))->get("registrations");

        if($regs->num_rows()==1){
            $reg = $regs->row_array();
            if($reg['registration_valid']==0){
                //check dulu di table doku ini udah pernah bayar lom
                $dokuCheck = $this->db->where("registration_code",$this->post("id"))->get("doku");
                $status = "Failed";
                if($dokuCheck->num_rows()==1){
                    $rowDoku = $dokuCheck->row_array();
                    if(strtolower($rowDoku['trxstatus'])=='success' ||strtolower($rowDoku['trxstatus'])=='requested' ){
                        $status = $rowDoku['trxstatus'];
                    }
                    if($status=="Requested"){
                        $status="Failed";
                    }
                }

                if($dokuCheck->num_rows()==0 || $status=="Failed"){
                    $payMets = $this->db->where("doku_id",$this->post("method"))->get("payment_method");
                    $payMet = $payMets->row_array();

                    $kata = $payMet["method_price"].".00".$this->mallid.$this->key."M5M".$reg["registration_id"];
                    $words = sha1($kata);

                    if($dokuCheck->num_rows()==0){
                        $this->db->insert("doku",array(
                            "registration_code" => $this->post("id"),
                            "transidmerchant" => "M5M".$reg["registration_id"],
                            "totalamount" => $payMet["method_price"],
                            "words"=> $words,
                            "trxstatus"=>"Requested",
                            "payment_channel"=> $this->post("method"),
                            "session_id" => $this->post("id"),
                            "payment_date_time" => date("Y-m-d H:i:s",$waktu),
                        ));
                    }else {
                        $this->db->where("registration_code",$this->post("id"))->update("doku",array(
//                        "transidmerchant" => $reg["registration_id"],
//                        "totalamount" => $payMet["method_price"],
                            "words"=> $words,
                            "trxstatus"=>"Requested",
//                        "payment_channel"=> $this->post("method"),
//                        "session_id" => sha1($this->post("id")),
                            "payment_date_time" => date("Y-m-d H:i:s",$waktu),
                        ));
                    }


                    $array = array(
                        "MALLID" => $this->mallid,
                        "CHAINMERCHANT" => 0,
                        "AMOUNT" => $payMet["method_price"],
                        "PURCHASEAMOUNT" => $payMet["method_price"],
                        "TRANSIDMERCHANT" => "M5M".$reg["registration_id"],
                        "WORDS" => $words,
//                    "kata" => $kata,
                        "REQUESTDATETIME" => date("YmdHis",time()),
                        "CURRENCY" => 360,
                        "PURCHASECURRENCY" => 360,
                        "SESSIONID" => $this->post("id"),
                        "NAME" => "",
                        "EMAIL" => "",
                        "BASKET" =>  $payMet['method_desc'],
                        "PAYMENTCHANNEL" => $payMet['doku_id']
                    );

                    $row = array(
                        "code" => 200,
                        "message" => "success",
                        "data" => $array
                    );
                }else{
                    $row = array(
                        "code" => 400,
                        "message" => "Transaction in progress, or already success"
                    );
                }
            }else{
                $row = array(
                    "code" => 400,
                    "message" => "Account Expired, create new account"
                );
            }
        }

        $this->response($row,200);
    }

    public function notifikasi_post(){
//        file_put_contents("./logs/iseng.txt",json_encode($_POST),FILE_APPEND);
        //ip doku
        if(in_array($this->input->ip_address(),array(
            "103.10.129.0",
            "103.10.129.1",
            "103.10.129.2",
            "103.10.129.3",
            "103.10.129.4",
            "103.10.129.5",
            "103.10.129.6",
            "103.10.129.7",
            "103.10.129.8",
            "103.10.129.9",
            "103.10.129.10",
            "103.10.129.11",
            "103.10.129.12",
            "103.10.129.13",
            "103.10.129.14",
            "103.10.129.15",
            "103.10.129.16",
            "103.10.129.17",
            "103.10.129.18",
            "103.10.129.19",
            "103.10.129.20",
            "103.10.129.21",
            "103.10.129.22",
            "103.10.129.23",
            "103.10.129.24"
        ))){
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
                            //update status registration
                            if($paymentchannel == "15"){
                                $this->db->where("registration_code",$session_id)->update("registrations",array(
                                    "registration_confirmation" => 1,
                                    "method_id" => 2
                                ));
                            }elseif ($paymentchannel == "05") {
                                $this->db->where("registration_code", $session_id)->update("registrations", array(
                                    "registration_confirmation" => 1,
                                    "method_id" => 3
                                ));
                            }
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

        $this->db->where("transidmerchant",$order_number)->update("doku",array(
            "response_code" => $status_code,
            "payment_channel" => $paymentchannel,
            "paymentcode" => $paymentcode,
            "session_id" => $session_id,
        ));
        if($paymentchannel == "05"){
            //send mail
            $this->_kirim($session_id,$paymentcode);
        }
        redirect("http://www.menang5miliar.com/thanks.php?id=".$session_id);
    }

    private function _kirim($code,$paymentcode){
        $regs = $this->db->where("registration_code",$code)->join("stores","registrations.store_id=stores.store_id")->from("registrations")->get();
        $reg = $regs->row_array();
        $txt = file_get_contents(FCPATH."notif.html");
        $tgl = substr($reg['competition_date'],8,2)."/".substr($reg['competition_date'],5,2)."/".substr($reg['competition_date'],0,4);
        $waktu = "12:45";
        if(in_array($reg['competition_session'],array(4,5))){
            $waktu = '13:45';
        }elseif(in_array($reg['competition_session'],array(6,7))){
            $waktu = '14:45';
        }
        $txt = str_replace(
            array("{{nodaftar}}","{{noktp}}","{{nama}}","{{lokasi}}","{{tanggal}}","{{jam}}","{{kodebayar}}"),
            array($reg['registration_code'],$reg['registration_nik'],$reg['registration_name'],$reg['store_name'],$tgl,$waktu,$paymentcode),$txt
        );
//        echo $txt;

        $this->load->library('email');
        $this->email->initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.sendgrid.net',
            'smtp_user' => 'goer',
            'smtp_pass' => 'goer1thea',
            'mailtype'  => 'html',
            'smtp_port' => 587,
            'crlf' => "\r\n",
            'newline' => "\r\n"
        ));

        $this->email->from('info@menang5miliar.com', 'Pembayaran Transfer');
        $this->email->to($reg['registration_email']);
        $this->email->subject('Pembayaran Lomba Balap Makan Ayam');
        $this->email->message($txt);
        $this->email->send();

        file_put_contents(FCPATH."logs/mail/payment-va-".date("Y-m-d").".txt",json_encode($this->email->print_debugger()),FILE_APPEND);
        return true;
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

    public function identifikasi_post(){
        $order_number = $this->post('TRANSIDMERCHANT');
        $amount = $this->post('AMOUNT');
        $channel = $this->post('PAYMENTCHANNEL');
        $id = $this->post('SESSIONID');
        $this->response($this->post(),200);

    }

    public function recheck_post(){
        $order_number = $this->post('TRANSIDMERCHANT');
        $amount = $this->post('AMOUNT');
        $channel = $this->post('PAYMENTCHANNEL');
        $id = $this->post('SESSIONID');
        $this->response($this->post(),200);

    }

    public function iseng_get(){
        echo "Stop";
    }

}
