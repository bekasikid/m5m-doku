<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_pertamina extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function redeem($user,$orders){
        $res = $this->db->where("user_id",$user['user_id'])->get("pertamina_outlet");
        if($res->num_rows()!=1){//tidak boleh menghasilkan lebih dr satu user
            return false;
        }else{
            $row_o = $res->row_array();
            if($row_o['outlet_redeem']==1){ //outlet sudah redeem tidak boleh redeem kembali
                return false;
            }elseif($row_o['outlet_target'] > $row_o['outlet_point']){ //outlet blum capai target tidak boleh redeem
                return false;
            }else{
                $this->db->set('outlet_redeem',1)->where('outlet_redeem',0)->where("user_id",$user['user_id'])->update("pertamina_outlet");
                if($this->db->affected_rows()==1){//mengantisipasi dua redeem bersamaan dalam satu waktu
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

    function approve(){

    }

    function reject(){

    }
}
?>