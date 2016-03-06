<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_order extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function addOrder($user_id,$provider_id,$total,$products){
        $rows = array();
        foreach($products as $product){
            $res_p = $this->db->where("provider_id",$provider_id)->get("providers");
            $row_p = $res_p->row_array();
            $row = array(
                'order_date' => date("Y-m-d"),
                'user_id' => $this->security->xss_clean($user_id),
                "product_id" => $product['product_id'],
                "product_point" => floor($product['product_price']/$row_p['provider_point']),
                "provider_id" => $provider_id,
                "vendor_id" => $product['vendor_id'],
                "product_price" => $product['product_price'],
                "point_rate" => $row_p['provider_point']
            );
            $this->db->insert("orders",$row);
            $row['order_id'] = $this->db->insert_id();
            $rows[] = $row;
        }
        return $rows;
    }
}
?>