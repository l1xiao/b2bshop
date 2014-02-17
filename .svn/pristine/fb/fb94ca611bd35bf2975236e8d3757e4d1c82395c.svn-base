<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品模型
class Order_model extends CI_Model{
	const TBL_GOODS = 'goods';
	const TBL_GOODS_PICTURES = 'goods_pictures';
	const TBL_COMMENTS_LIST = 'comments_list';
	const TBL_ORDER = 'order';
	const TBL_GOODS_ORDER = 'goods_order';

	#获取分页数据
	public function list_order($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_ORDER);
		return $query->result_array();
	}

	#计算订数量用来分页
	public function count_order(){
		return $this->db->count_all(self::TBL_ORDER);
	}

	#增加order
	public function add_order($data){
		return $this->db->insert(self::TBL_GOODS_ORDER,$data);
	}

	#获取订单信息
	public function get_order($order_id){
		$condition['order_id'] = $order_id;
		$query = $this->db->where($condition)->get(self::TBL_ORDER);
		return $query->row_array();
	}

	#选择订单状态
	public function order_change($order_id,$order_status){
		$data = array('order_status' => $order_status);		
		$query = $this->db->where('order_id', $order_id)->update(self::TBL_ORDER, $data);
		return $query > 0 ? 1 : 0;

	}
}

