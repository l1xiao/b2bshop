<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 商品类别模型
class Category_model extends CI_Model{

	const TBL_CATE = "category";

	/**
	 * @access public
	 * @param $pid 节点的id
	 * @param array 返回该节点的所有后代节点
	 */
	public function list_cate($pid = 0){
		#获取所有的记录
		$query = $this->db->get(self::TBL_CATE);
		$cates = $query->result_array();
		#对类别进行重组，并返回
		return $this->_tree($cates,$pid);
	}



	/**
	 *@access private
	 *@param $arr array 要遍历的数组
	 *@param $pid 节点的pid，默认为0，表示从顶级节点开始
	 *@param $level int 表示层级 默认为0
	 *@param array 排好序的所有后代节点
	 */
	private function _tree($arr,$pid = 0,$level = 0){
		static $tree = array(); #用于保存重组的结果,注意使用静态变量
		foreach ($arr as $v) {
			if ($v['parent_id'] == $pid){
				//说明找到了以$pid为父节点的子节点,将其保存
				$v['level'] = $level;
				$tree[] = $v;
				//然后以当前节点为父节点，继续找其后代节点
				$this->_tree($arr,$v['cat_id'],$level + 1);
			}
		}

		return $tree;
	}


	#添加分类信息
	public function add_category($data){
		return $this->db->insert(self::TBL_CATE,$data);
	}

	#获取单条信息
	public function get_cate($cat_id){
		#这里用了关联数组，所以键名和变量就符合了where的条件
		$condition['cat_id'] = $cat_id;
		$query = $this->db->where($condition)->get(self::TBL_CATE);
		#返回单条记录
		return $query->row_array();
	}

	#更新
	public function update_cate($data,$cat_id){
		$condition['cat_id'] = $cat_id;
		return $this->db->where($condition)->update(self::TBL_CATE,$data);
	}

	#删除
	public function delete_cate($cat_id){
		$condition['cat_id'] = $cat_id;
		$query = $this->db->where($condition)->delete(self::TBL_CATE);
		if ($query && $this->db->affected_rows() > 0) {
			# code...
			return true;
		} else {
			# code...
			return false;
		}	
	}
}