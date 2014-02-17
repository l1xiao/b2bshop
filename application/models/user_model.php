<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品模型
class User_model extends CI_Model{
	const TBL_USER = 'user';
	const TBL_USER_COMMENTS = 'user_comments';

	#获取用户分页数据
	public function list_user($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_USER);
		return $query->result_array();
	}
	
	#登录验证
	public function login_user($username,$password){
		$condition['user_name'] = $username;
		$condition['password'] = md5($password);
		$query = $this->db->where($condition)->get(self::TBL_USER);
		return $query->row_array();
	}

	#计算订数量用来分页
	public function count_user(){
		return $this->db->count_all(self::TBL_USER);
	}

	#增加order
	public function add_user($data){
		return $this->db->insert(self::TBL_USER,$data);
	}

	#获取订单信息
	public function get_user($user_id){
		$condition['user_id'] = $user_id;
		$query = $this->db->where($condition)->get(self::TBL_USER);
		return $query->row_array();
	}

	#获取评论分页数据
	public function list_comments($limit,$offset){
		$query = $this->db->limit($limit,$offset)->get(self::TBL_USER_COMMENTS);
		return $query->result_array();
	}

	#计算订数量用来分页
	public function count_comments(){
		return $this->db->count_all(self::TBL_USER_COMMENTS);
	}

	#选择评论是否显示
	public function comments_delete($comments_id){
		$data = array('is_showed' => '-1');		
		$query = $this->db->where('id', $comments_id)->update(self::TBL_USER_COMMENTS, $data);
		return $query > 0 ? 1 : 0;

	}
	public function comments_show($comments_id){
		$data = array('is_showed' => '1');		
		$query = $this->db->where('id', $comments_id)->update(self::TBL_USER_COMMENTS, $data);
		return $query > 0 ? 1 : 0;
	}

}