<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_outlet extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function addOutlet($user,$distributor,$kode,$name,$address,$city,$pic,$phone,$target,$type=""){
        $res = $this->db->where('outlet_kode', $kode)->get("pertamina_outlet");
        if($res->num_rows()==0){
            $row = array(
                'user_id' => $user,
                'distributor_id' => $distributor,
                'outlet_kode' => $kode,
                'outlet_name' => $name,
                'outlet_type' => $type,
                'outlet_address' => $address,
                'outlet_city' => $city,
                'outlet_pic' => $pic,
                'outlet_phone' => $phone,
                'outlet_target' => $target
            );
            $this->db->insert("pertamina_outlet",$row);
            $row['outlet_id'] = $this->db->insert_id();
            return $row;
        }else{
            $row_o = $res->row_array();
            $row = array(
//                'outlet_name' => $name,
//                'outlet_type' => $type,
                'outlet_address' => $address,
                'outlet_city' => $city,
                'outlet_pic' => $pic,
                'outlet_phone' => $phone,
//                'outlet_target' => $target
            );
            if(!empty($target)){
                $row['outlet_target'] = $target;
            }
            $this->db->where('outlet_id',$row_o['outlet_id']);
            $this->db->update("pertamina_outlet",$row);
//            $row['outlet_id'] = $this->db->insert_id();
            return array_merge($row_o,$row);
        }


    }
}
?>