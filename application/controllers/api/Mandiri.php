<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * doku ip : 103.10.129.0 - 103.10.129.24
 */
class Mandiri extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function payment_post()
    {

        $row = base64_decode($this->post("text"));
        file_put_contents(FCPATH."logs/mandiri/".date("Y-m-d")."-log.txt",date("Y-m-d H:i:s")."-".$row."\r\n\r\n\r\n",FILE_APPEND);
//        $this->db->insert("mandiri_post", array("post_date" => date("Y-m-d H:i:s"), "post_text" => $row));
        $this->parsing_data($row);
        $this->response($row, 200);
    }

    public function parsing_data($text)
    {

        $rows = array();
        $trx = "";
        $data = explode("\n", $text);
        $rows[] = $data;
        $n = 0;
        foreach ($data as $baris) {
            if ($n == 1) {
                $trx .= $baris;
                $n = 0;
                //mulai parsing data
                $field = explode(" ", $trx);
                $tgl = explode("/", $field[0]);
                $date = $tgl[2] . "-" . $tgl[1] . "-" . $tgl[0] . " " . $field[1];
                //ambil 3 row terakhir
                $debit = str_replace(",", "", $field[count($field) - 3]);
                $credit = str_replace(",", "", $field[count($field) - 2]);
                if (!is_double($debit)) {
                    $debit = $credit;
                    $credit = str_replace(",", "", $field[count($field) - 1]);
                }
                $isi[] = array(
                    "credit" => $credit,
                    "debit" => $debit
                );

                if ((intval($credit) != 0) && (intval($credit)<160000)) {
//                    $tampil[] = array(
//                        "mandiri_datetime" => $date . " " . $field[1],
//                        "mandiri_credit" => doubleval($credit),
//                        "created_date" => date("Y-m-d H:i:s")
//                    );

                    $desc = $field;
                    unset($desc[0]);unset($desc[2]);unset($desc[count($field) - 1]);unset($desc[count($field) - 2]);
                    $rowM = $this->db->where("mandiri_datetime", $date . " " . $field[1])->where("mandiri_credit", intval($credit))->get("mandiri");
                    if ($rowM->num_rows() == 0) {
                        $this->db->insert("mandiri", array(
                            "mandiri_datetime" => $date . " " . $field[1],
                            "mandiri_credit" => intval($credit),
                            "mandiri_desc" => implode(" ",$desc),
                            "date_created" => date("Y-m-d H:i:s")
                        ));
                    }
                }
                $trx = "";
            }
            $field = explode(" ", $baris);
            if ($n == 0) {
                $tgl = explode("/", $field[0]);//check tanggal di field pertama
                if (count($tgl) == 3) {
                    $trx = $baris;
                    $n = 1;
                }
            }
        }
    }


}
