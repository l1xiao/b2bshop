<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//品牌模型
class Brand_model extends CI_Model{

	const TBL_BRAND = 'brand'; //此处不需要加前缀

	public function add_brand($data){
		return $this->db->insert(self::TBL_BRAND,$data);
	}

	#获取分页数据
	public function list_brand($limit = '',$offset = ''){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_BRAND);
		return $query->result_array();
	}

	#获得所有brand品牌
	public function list_brands(){
		$query = $this->db->get(self::TBL_BRAND);
		return $query->result_array();
	}

	#统计商品品牌的总数
	public function count_brand(){
		return $this->db->count_all(self::TBL_BRAND);
	}

	#获取一条商品品牌信息
	public function get_brand($id){
		$condition['brand_id'] = $id;
		$query = $this->db->where($condition)->get(self::TBL_BRAND);
		return $query->row_array();
	}
}