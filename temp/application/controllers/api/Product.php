<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . 'libraries/REST_Controller.php';


class Product extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model("model_product");
    }

    function getProducts_get(){
        $res = $this->db->get('pertamina_products');
        $this->response($res->result_array(), 200);
    }

    function getProduct_get(){
        $this->db->where("product_id",$this->get("id"));
        $res = $this->db->get('pertamina_products');
        $this->response($res->row_array(), 200);
    }

    function addProduct_post()
    {
        $name = $this->post('name');
        $point = $this->post('point');
        $res = $this->model_product->addProduct($name,$point);
        $this->response($res,200);
    }

    function importProduct_post(){
        $products = array();
        $user = $this->session->userdata("user");
        if($user){
            $filename = $_FILES['file']['name'];
            $path = $_FILES['file']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $filename = date("YmdHis")."_".md5($this->security->xss_clean($filename)).".".$ext;
            $destination = FCPATH.'assets/upload/' . $filename;
            move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );

            $this->load->library('excel');
            $objPHPExcel = PHPExcel_IOFactory::load($destination);
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();

                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getFormattedValue();

                //header will/should be in row 1 only. of course this can be modified to suit your need.
                if ($row == 1) {
                    $header[$row][$column] = $data_value;
                } else {

                    $arr_data[$row][$column] = $data_value;
                }
            }
            foreach($arr_data as $key=>$val){
                if(!empty($val['A'])){
                    $point = isset($val['Q'])?$val['Q']:0;
                    $products[$key] = $this->model_product->addProduct(trim($val['A']),trim($val['B']),$point,$val['O'],$val['P']);
                }
            }
        }
        $this->response($products,200);
    }

    function importProductOri_post(){
        $products = array();
        $user = $this->session->userdata("user");
        if($user){
            $filename = $_FILES['file']['name'];
            $path = $_FILES['file']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $filename = date("YmdHis")."_".md5($this->security->xss_clean($filename)).".".$ext;
            $destination = FCPATH.'assets/upload/' . $filename;
            move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );

            $this->load->library('excel');
            $objPHPExcel = PHPExcel_IOFactory::load($destination);
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
//                if($column=='N'){
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getFormattedValue();
//                }else{
//                    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
//                }

                //header will/should be in row 1 only. of course this can be modified to suit your need.
                if ($row == 1) {
                    $header[$row][$column] = $data_value;
                } else {

                    $arr_data[$row][$column] = $data_value;
                }
            }
            foreach($arr_data as $key=>$val){
                if(!empty($val['A'])){
                    if(is_numeric($val['A'])){
                        $products[$key] = $this->model_product->addProduct($val['D'],$val['B'],$val['C']);
                    }
                }
            }
        }
        $this->response($products,200);
    }
}
