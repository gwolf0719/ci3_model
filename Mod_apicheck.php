<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
                        
class Mod_apicheck extends CI_Model {
    

    // 資料輸入時過濾非 html 字元
    function chk_get_post_requred_hashtml($getpost,$requred){
        $data = $this->chk_get_post_requred($getpost,$requred);
        $this->load->library('str');
        foreach ($data as $key => $value) {
            # code...
            $data[$key] = $this->str->clean_html($value);
        }
        return $data;
    }
    // 驗證必填
    function chk_get_post_requred($getpost,$requred){
        $data = $this->getpost->getpost_array($getpost, $requred);
        if ($data == false) {
            $json_arr['sys_code'] = '000';
            $json_arr['sys_msg'] = '資料不足';
            $json_arr['requred'] = $this->getpost->report_requred($requred);
            echo json_encode($json_arr);
            exit();
        } 
        return $data;
    }
    // 泛用型確認資料存在
    function chk_data($where,$table,$msg = '') {
        if(!$this->mod_db->chk_once($where,$table)){
            $json_arr['sys_code'] = '404';
            if($msg != ""){
                $json_arr['sys_msg'] = $msg;
            }else{  
                $json_arr['sys_msg'] = '資料不存在';
            }
            echo json_encode($json_arr);
            exit();
        }
    }
    // 泛用行確認資料重複
    function chk_repeat($where,$table,$msg = ''){
        if($this->mod_db->chk_once($where,$table)){
            $json_arr['sys_code'] = '500';
            if($msg != ""){
                $json_arr['sys_msg'] = $msg;
            }else{  
                $json_arr['sys_msg'] = '資料重複';
            }
            echo json_encode($json_arr);
            exit();
        }
    }
             
                        
}
                        
/* End of file Mod_apicheck.php */
    
                        
