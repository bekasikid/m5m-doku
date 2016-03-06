<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_trx extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function excelDate($EXCEL_DATE){
        $UNIX_DATE = ($EXCEL_DATE - 25569) * 86400;
        return gmdate("Y-m-d H:i:s", $UNIX_DATE);
    }

    function dateConvert($date){
        if(strpos($date,"/")!==false){
            $tgl = explode(" ",$date);
            $tgl[0] = explode("/",$tgl[0]);
            return $tgl[0][2]."-".$tgl[0][1]."-".$tgl[0][0]." ".$tgl[1];
        }else{
            return $date;
        }
    }

    function addTrx($trxno, $outlet, $product, $product_name, $date, $liter ,$doos, $program, $isexcel=0){
        $res_p = $this->db->where("product_code",$product)->get("pertamina_products");
        if($res_p->num_rows()==0){
            $arr_p = array(
                "product_code" => $product,
                "product_name" => $product_name,
                "product_point" => 0
            );
            $this->db->insert("pertamina_products",$arr_p);
            $res_p = $this->db->where("product_code",$product)->get("pertamina_products");
        }

        if($res_p->num_rows()){
            $row_p = $res_p->row_array();
            $res_o = $this->db->where("outlet_kode",$outlet)->get("pertamina_outlet");
            if($res_o->num_rows()){
                $row_o = $res_o->row_array();
                $point =  intval($doos)*$row_p['product_point'];
                $res_trx = $this->db->where("trx_no",$trxno)->where("product_id",$row_p['product_id'])->get("pertamina_transactions");

                $liter = $doos * $row_p['product_volume'];
                if($res_trx->num_rows()==0){
                    $row = array(
                        'trx_no' => $trxno,
                        'trx_date' => $isexcel?$this->excelDate($date):$this->dateConvert($date),
                        'outlet_id' => $row_o['outlet_id'],
                        'product_id' => $row_p['product_id'],
                        'trx_liter' => $liter,
                        'trx_doos' => $doos,
                        'program_id' => $program,
                        'trx_point' => $point
                    );
                    $this->db->insert("pertamina_transactions",$row);
                    $row['trx_id'] = $this->db->insert_id();

                    //update volume
                    $this->db->where("outlet_id",$row_o['outlet_id'])->set("outlet_volume","outlet_volume+".$liter,false)->update("pertamina_outlet");

                    if($point>0){
                        //update volume yg dapet point
                        $this->db->where("outlet_id",$row_o['outlet_id'])->set("outlet_volume_point","outlet_volume_point+".$point,false)->update("pertamina_outlet");
                        //update outlet point
                        $this->db->where("outlet_id",$row_o['outlet_id'])->set("outlet_point","outlet_point+".$point,false)->update("pertamina_outlet");
                        $user = $this->session->userdata("user");
                        $this->db->where("user_id",$row_o['user_id'])->where("provider_id",$user['provider_code']);
                        $this->db->set("point","point+".$point,false)->update("users_points");
                        //update user point

                        //get program
                        $this->db->where("outlet_id" , $row_o['outlet_id'])->where("program_id" , $program);
                        $this->db->set("stats_points" , "stats_points+".$point,false);
                        $this->db->set("stats_volume" , "stats_volume+".$liter,false);
                        $this->db->set("stats_trx_num" , "stats_trx_num+1",false);
                        $this->db->set("stats_volume_points" , "stats_volume_points+".$point,false);
                        $this->db->update("pertamina_stats");
                        if($this->db->affected_rows()==0){
                            $row_stat = array(
                                "outlet_id" => $row_o['outlet_id'],
                                "program_id" => $program,
                                "stats_points" => $point,
                                "stats_trx_num" => 1,
                                "stats_target" => 0,
                                "stats_volume" => $liter,
                                "stats_volume_points" => $point
                            );
                            $this->db->insert("pertamina_stats",$row_stat);
                        }
                    }

                    return $row;
                }else{
                    $this->db->where("trx_no",$trxno)->where("product_id",$row_p['product_id'])->set('trx_date',$isexcel?$this->excelDate($date):$this->dateConvert($date))->update("pertamina_transactions");
                }
            }
        }
        return false;
    }

    function addLog($order,$desc){
        $user = $this->session->userdata("user");
        $row = array(
            'order_id' => $order,
            'user_id' => $user['user_id'],
            'log_actor' => $user['user_role'],
            'log_desc' => $desc
        );
        $this->db->insert("orders_logs",$row);
    }

    function getRedeem($id){
        $select = "orders.*, users_points.point, providers.provider_name,users.user_name,users.user_address,users.user_phone,
                    pertamina_outlet.outlet_id,pertamina_outlet.outlet_target,pertamina_outlet.outlet_point,
                    products.product_maker, products.product_name,payment_method.payment_type";
        $this->db->select($select);
        $this->db->from("orders")->join("users_points", "orders.user_id = users_points.user_id AND orders.provider_id = users_points.provider_id");
        $this->db->join("products", "products.product_id=orders.product_id");
        $this->db->join("users", "users.user_id=orders.user_id");
        $this->db->join("pertamina_outlet", "orders.user_id=pertamina_outlet.user_id");
        $this->db->join("providers", "orders.provider_id=providers.provider_id");
        $this->db->join("payment_method", "orders.order_paid_method=payment_method.payment_id", "LEFT");
        $this->db->where("orders.order_id", $id);
        $res = $this->db->get();
        return $res;
    }

    function getRedeems($type){
        $select = "orders.*, users_points.point, pertamina_outlet.outlet_kode,pertamina_outlet.outlet_name,
                    pertamina_outlet.outlet_target,pertamina_outlet.outlet_point,pertamina_distributors.distributor_name,
                    products.product_maker, products.product_name";
        $this->db->select($select);
        $this->db->from("orders")->join("users_points", "orders.user_id = users_points.user_id AND orders.provider_id = users_points.provider_id");
        $this->db->join("pertamina_outlet", "orders.user_id=pertamina_outlet.user_id");
        $this->db->join("products", "products.product_id=orders.product_id");
        $this->db->join("pertamina_distributors", "pertamina_distributors.distributor_id=pertamina_outlet.distributor_id");

        if(empty($type)){
            $res = $this->db->get();
            return $res->result_array();
        }else{
            if($type=='total'){
                $res = $this->db->count_all_results();
            }
            return $res;
        }
    }
}
?>