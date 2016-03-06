<?php
/**
 * Created by PhpStorm.
 * User: AINUL
 * Date: 6/19/2015
 * Time: 09:58
 */

class Model_user extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function login($email,$password){
        $email = $this->security->xss_clean($email);
        $password = $this->security->xss_clean($password);
        $this->db->where("user_email",$email);
        $res = $this->db->get('users');
        if($res->num_rows()==1){
            $row = $res->row_array();
            if($row['user_password']==$password){
                $this->db->select("users_points.*,providers.provider_name")->join("providers","providers.provider_id=users_points.provider_id");
                $provs = $this->db->where("users_points.user_id",$row['user_id'])->get("users_points");
                if($provs->num_rows()){
                    $row['providers'] = $provs->result_array();
                    $row['active_provider'] = $row['providers'][0];
                }else{
                    $row['providers'] = array();
                    $row['active_provider'] = null;
                }

                $date= date("Y-m-d H:i:s");
                $update = array(
                    "last_login" => $date,
                    "last_ip" => $this->input->ip_address(),
                    "user_hash" => md5(sha1($email.$this->input->ip_address().$date))
                );
                $this->db->where("user_id",$row['user_id'])->update("users",$update);
                $row['last_login'] = $update['last_login'];
                unset($row['user_password']);
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function addDistributor($userid,$kode,$name,$address){
        $distributor = array(
            "user_id" => $userid,
            "distributor_code" => $kode,
            "distributor_name" => $name,
            "distributor_address" => $address
        );
        $this->db->insert("pertamina_distributors",$distributor);
        $distributor['distributor_id'] = $this->db->insert_id();
        return $distributor;
    }

    function addUser($name,$email,$password,$role,$parent,$provider,$kode,$address='',$postcode='',$phone='',$city=''){
        $res = $this->db->where("user_username", $kode)->get("users");
        if($res->num_rows()==0){
            $row = array(
                'user_name' => $name,
                "user_username" => $kode,
                'user_email' => $email,
                'user_password' => $password,
                'user_image' => 'image_20150620212315_96797f7e012cadfc361f558ac44524b2.jpg',
                'user_role' => $role,
                'user_status' => 1,
                'user_parent' => $parent,
                'provider_code' => $provider,
                'user_address' => $address,
                'user_postcode' => $postcode,
                'user_phone' => $phone,
                'user_city' => $city
            );
            $this->db->insert("users",$row);
            $row['user_id'] = $this->db->insert_id();

            $row_p = array(
                "user_id" => $row['user_id'],
                "provider_id" => $provider,
                "point" => 0
            );
            $this->db->insert("users_points",$row_p);
            return $row;
        }else{
            $row_u = $res->row_array();

            $row = array(
//                'user_name' => $name,
//                "user_username" => $kode,
//                'user_email' => $email,
//                'user_password' => $password,
//                'user_image' => 'image_20150620212315_96797f7e012cadfc361f558ac44524b2.jpg',
//                'user_role' => $role,
//                'user_status' => 1,
//                'user_parent' => $parent,
//                'provider_code' => $provider,
                'user_address' => $address,
                'user_postcode' => $postcode,
                'user_phone' => $phone,
                'user_city' => $city
            );
            $this->db->where("user_id",$row_u['user_id']);
            $this->db->update("users",$row);
            return array_merge($row_u,$row);
        }

    }

    function checkDistributor($code,$name,$address,$provider){
        if(strlen(trim($code))>=6){
            $res = $this->db->where("distributor_code",trim($code))->get("pertamina_distributors");
            if($res->num_rows()==0){
                $row = $this->addUser($name,$code,'12345678','distributor',$provider,$provider,$code,$address,'','','');
                return $this->addDistributor($row['user_id'],$code,$name,$address);
            }
            return $res->row_array();
        }else{
            return false;
        }

    }
}
?>