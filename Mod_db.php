<?php 
/********************************
 * CI3 資料庫通用擴充外掛
 * 不支援 CI4
 * 2021-01-05 @ James 查詢逗點分隔內容
 * 2020-09-21 @ James 設定範圍搜尋
 */
defined('BASEPATH') OR exit('No direct script access allowed');
                        
class Mod_db extends CI_Model {
  // 查詢逗點分隔內容
    function find_col_explode($table,$column,$value,$where=""){
        $db = $this->db;
        if($where != ""){
            $db->where($where);
        }
        $datas = $db->like($column,$value)->get($table)->result_array();
        $new_datas = [];
        foreach ($datas as $k => $v) {
            # code...
            if(in_array($value,explode(',',$v[$column]))){
                $new_datas[] = $v;
            }
        }
        return $new_datas;
    }


    /****
     * 設定範圍搜尋 如果沒有代值會找 get_post 返回 where 條件式
     */
    function set_where_between($where=array(),$datetime_col, $start="", $end=""){
        if($start == ""){
            $start = $this->input->get_post('start');
        }
        if($end == ""){
            $end = $this->input->get_post('end');
        }
        if($start != ""){
            $where[$datetime_col." >="] = $start;
        }
        if($end != ""){
            $where[$datetime_col." <="] = $end;
        }
        return $where;
    }
    function set_where_getpost($source_where=array(),$cols=array()){
        $where = array();
        foreach($cols as $k=>$v){
            if($this->input->get_post($v) != ""){
                $where[$v] = $this->input->get_post($v);
            }
        }
        $where = array_merge($source_where,$where);
        return $where;
    }

    /**
     * 自動判斷要更新或是新增
     */
    function insert_or_update($where,$set,$table){
        if($this->db->where($where)->count_all_results($table) == 0){
            $set = array_merge($where,$set);
            $this->db->insert($table,$set);
        }else{
            $this->db->update($table,$set,$where);
        }

    }

    /*******
     * 驗證資料存在與否
     */
    function chk_once($where,$table){
        $this->db->where($where);
        if($this->db->count_all_results($table) == 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /*******
     * 資料組合
     * full_data 已經拿到的二維陣列資料
     * extra_table 擴充查詢表
     * mapping_key 兩份資料的關聯鍵
     * extra_col  要增加的欄位（必須要在 extra_table 中有）請用單維陣列
     */
    function table_merge($full_data,$extra_table,$mapping_key,$extra_col){
        $ext = [];
        foreach($this->db->get($extra_table)->result_array() as $key => $val){
            $ext[$val[$mapping_key]] = $val;
        }
        $datalist = [];
        foreach ($full_data as $key => $value) {
            # code...
            $datalist[$key] = $value;
            foreach ($extra_col as $k => $v) {
                if(isset($ext[$value[$mapping_key]])){
                    $datalist[$key][$v] = $ext[$value[$mapping_key]][$v];
                }else{
                    $datalist[$key][$v] = null;
                }
            }
        }
        return $datalist;
    }

    // 取得指定資料的單一欄位
    function get_once_col_value($where,$table,$col){
        $data = $this->db->where($where)->get($table)->row_array();
        if(isset($data[$col])){
            return $data[$col];
        }else{
            return false;
        }
    }
                            
                        
}
                        
/* End of file Mod_db.php */
    
                        
