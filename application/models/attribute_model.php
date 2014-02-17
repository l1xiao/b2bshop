<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品属性模型

class Attribute_model extends CI_Model{

	const TBL_ATTR = 'attribute';
	const TBL_GOODS_ATTR = 'goods_attr';


	public function add_attrs($data){
		return $this->db->insert(self::TBL_ATTR,$data);
	}

	#获取分页数据
	public function list_attrs($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_ATTR);
		return $query->result_array();
	}

	#计算商品数量用来分页
	public function count_attrs(){
		return $this->db->count_all(self::TBL_ATTR);
	}


	#获取指定类型下面所有的属性
	public function get_attrs($type_id){
		$condition['type_id'] = $type_id;
		$query = $this->db->where($condition)->get(self::TBL_ATTR);
		return $query->result_array();
	}

	public function get_goods_attrs($goods_id="")
	{
		$condition['goods_id'] = $goods_id;
		$query = $this->db->where($condition)->get(self::TBL_GOODS_ATTR);
		return $query->result_array();
	}
}