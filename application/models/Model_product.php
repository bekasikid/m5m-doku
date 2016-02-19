<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_product extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function addProduct($code,$name,$point,$volume,$unit){
        if(empty($code) && empty($name))
        {
            return false;
        }
        $res = $this->db->where("product_code",$code)->get("pertamina_products");
        if($res->num_rows()==0){
            $row = array(
                'product_code' => $code,
                'product_name' => $name,
                'product_point' => $point,
                'product_volume' => $volume,
                'product_unit' => $unit
            );
            $this->db->insert("pertamina_products",$row);
            $row['product_id'] = $this->db->insert_id();
        }else{
            $row = array(
//                'product_code' => $code,
                'product_name' => $name,
//                'product_point' => $point,
                'product_volume' => $volume,
                'product_unit' => $unit
            );
            $row_p = $res->row_array();
            $this->db->where("product_id",$row_p['product_id'])->update("pertamina_products",$row);
//            $row['product_id'] = $this->db->insert_id();
        }

        return $row;
    }

    function addProductOri($code,$name,$point){

        $row = array(
            'product_code' => $code,
            'product_name' => $name,
            'product_point' => $point
        );
        $this->db->insert("pertamina_products",$row);
        $row['product_id'] = $this->db->insert_id();
        return $row;
    }

    function editProduct($id,$maker,$name,$desc,$image,$cat){

        $row = array(
            'product_maker' => $maker,
            'product_name' => $name,
            'product_desc' => $desc,
            'cat_id' => $cat['cat_id'],
            'product_image' => 'default.png'
        );
        $this->db->where("product_id",$id);
        $this->db->update("products",$row);
        $row['product_id'] = $this->db->insert_id();
        return $row;
    }

    function addProducts($maker,$name,$image,$price,$status,$detail,$desc,$vendor,$cat){
        $image = $this->security->xss_clean($image);
        $row = array(
            'product_maker' => $this->security->xss_clean($maker),
            'product_name' => $this->security->xss_clean($name),
            'product_price' => $this->security->xss_clean($price),
            'product_image' => empty($image)?"default.png":$image,
            'product_status' => $this->security->xss_clean($status),
            'product_detail' => $detail,
            'product_description' => $desc,
            'vendor_id' => $this->security->xss_clean($vendor),
            'category_id' => $cat
        );
        $this->db->insert("products",$row);
        $row['product_id'] = $this->db->insert_id();
        return $row;
    }

    function updateProducts($pid,$maker,$name,$image,$price,$status,$detail,$desc,$vendor,$cat){
        $image = $this->security->xss_clean($image);
        $row = array(
            'product_maker' => $this->security->xss_clean($maker),
            'product_name' => $this->security->xss_clean($name),
            'product_price' => $this->security->xss_clean($price),
            'product_image' => empty($image)?"default.png":$image,
            'product_status' => $this->security->xss_clean($status),
            'product_detail' => $detail,
            'product_description' => $desc,
            'vendor_id' => $this->security->xss_clean($vendor),
            'category_id' => $cat
        );
        $this->db->where("product_id",$pid);
        $this->db->update("products",$row);
        $row['product_id'] = $pid;
        return $row;
    }
}
?>