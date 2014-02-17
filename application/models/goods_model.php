<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品模型
class Goods_model extends CI_Model{
	const TBL_GOODS = 'goods';
	const TBL_GOODS_PICTURES = 'goods_pic';
	const TBL_COMMENTS_LIST = 'comments_list';
	const TBL_ORDER_GOODS = 'order_goods';

	#添加商品，注意返回值,是新插入记录的id
	public function add_goods($data){
		$query = $this->db->insert(self::TBL_GOODS,$data);
		return $query ? $this->db->insert_id() : false;
	}

	#获取分页数据
	public function list_goods($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_GOODS);
		return $query->result_array();
	}

	#获取某个分类的所有商品
	public function get_allGoods($cat_id) {
		if ($cat_id == 0) {
			$query = $this->db->get(self::TBL_GOODS);
		} else {
			$condition['cat_id'] = $cat_id;
			$query = $this->db->where($condition)->get(self::TBL_GOODS);
		}
		return $query->result_array();
	}

	public function get_bestgoods($limit = 5) {
		$condition['is_best'] = '1';
		$query = $this->db->where($condition)->limit($limit)->get(self::TBL_GOODS);
		return $query->result_array();
	}

	#计算商品数量用来分页
	public function count_goods(){
		return $this->db->count_all(self::TBL_GOODS);
	}


	#获取一条商品信息
	public function get_goods($goods_id){
		$condition['goods_id'] = $goods_id;
		$query = $this->db->where($condition)->get(self::TBL_GOODS);
		return $query->row_array();
	}

	#获取订单内商品信息
	public function get_order_goods($order_id){
		$condition['order_id'] = $order_id;
		$query = $this->db->where($condition)->get(self::TBL_ORDER_GOODS);
		return $query->result_array();
	}



	#获取商品属性信息
	public function get_attrs($goods_id,$type_id){
		$sql = "select a.*,b.attr_value as default_value from ci_attribute as a left join ci_goods_attr as b on a.attr_id = b.attr_id
		and b.goods_id = $goods_id where a.type_id = $type_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	#修改单条商品信息
	public function update_goods($data){
		$query = $this->db->where('goods_id',$data['goods_id'])->update(self::TBL_GOODS,$data);
		return $query ? $data['goods_id'] : false;
	}

	#计算评论数量用来分页
	public function count_comments(){
		return $this->db->count_all(self::TBL_COMMENTS_LIST);
	}

	#获取分页数据
	public function list_comments($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_COMMENTS_LIST);
		return $query->result_array();
	}

	#选择评论是否显示
	public function comments_delete($comments_id){
		$data = array('is_showed' => '-1');		
		$query = $this->db->where('comments_id', $comments_id)->update(self::TBL_COMMENTS_LIST, $data);
		return $query > 0 ? 1 : 0;

	}
	public function comments_show($comments_id){
		$data = array('is_showed' => '1');		
		$query = $this->db->where('comments_id', $comments_id)->update(self::TBL_COMMENTS_LIST, $data);
		return $query > 0 ? 1 : 0;
	}

	public function get_comments($comments_id){
		$query = $this->db->where(array('comments_id'=>$comments_id))->get(self::TBL_COMMENTS_LIST);
		return $query->row_array();
	}

	public function get_comments_by_goods_id($goods_id='') {
		$query = $this->db->where(array('goods_id'=>$goods_id))->get(self::TBL_COMMENTS_LIST);
		return $query->result_array();
	}

	public function reply_comments($comments_id,$comments){
		$data = array(
			'admin_reply' => $comments,
			);		
		$query = $this->db->where('comments_id', $comments_id)->update(self::TBL_COMMENTS_LIST, $data);
		return $query > 0 ? 1 : 0;
	}
	#获得那些未删除的图片
	public function get_picId($goods_id) {
		$conditions = array('goods_id' => $goods_id);
		$this->db->where('is_delete !=', 1);
		$query = $this->db->where($conditions)->get(self::TBL_GOODS_PICTURES);
		return $query->result_array();
	}

	public function get_picByid($goods_pic_id) {
		$conditions = array('goods_pic_id' => $goods_pic_id);
		$query = $this->db->where($conditions)->get(self::TBL_GOODS_PICTURES);
		return $query->result_array();
	}

	public function thumb($goods_pic_id,$thumb) {
		$conditions = array('goods_pic_id' => $goods_pic_id);
		$data = array(
			'pic_thumb_name' => $thumb,
			);
		$query = $this->db->where($conditions)->update(self::TBL_GOODS_PICTURES,$data);
		return $query > 0 ? 1 : 0;		
	}

	public function photo_state($goods_pic_id, $pic_usage, $pic_description) {
		$conditions = array('goods_pic_id' =>$goods_pic_id);
		$data = array(
			'pic_usage' 		=> $pic_usage, 
			'pic_description'	=> $pic_description,
			);				
		$query = $this->db->where($conditions)->update(self::TBL_GOODS_PICTURES,$data);
		return $query > 0 ? 1 : 0;
	}

	public function delete_pic($goods_pic_id) {
		$conditions = array('goods_pic_id' => $goods_pic_id);
		$data = array(
			'is_delete' 		=> 1, 
			);
		$query = $this->db->where($conditions)->update(self::TBL_GOODS_PICTURES,$data);
		return $query > 0 ? 1 : 0;	
	}

	public function get_goodsId($goods_pic_id) {
		$conditions = array('goods_pic_id'=> $goods_pic_id);
		$query = $this->db->where($conditions)->get(self::TBL_GOODS_PICTURES);
		return $query->row_array();
	}

	public function  search($goods_name) {
		$this->db->like('goods_name', $goods_name);
		$query = $this->db->get(self::TBL_GOODS);
		return $query->result_array(); 
	}
}