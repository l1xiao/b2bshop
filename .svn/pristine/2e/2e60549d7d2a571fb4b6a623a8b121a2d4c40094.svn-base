<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Category_model extends CI_Model{
	const TBL_CATE = "category";
	public function list_cate($pid = 0){
		#获取所有记录
		$query = $this->db->get(self::TBL_CATE);
		$cates = $query->result_array();
		#对数组进行重组
		return $this->tree($cates,$pid);
	}
	
	private function _tree($arr,$pid = 0,level =0){
		static $tree = array();
		foreach ($arr as $v){
			#判断其父级id是否是想要的
			if($v['parent_id'] == $pid){
				$v['level'] = $level;
				$tree[] = $v;
				$this->_tree($tree,$v['cat_id'],$level+1);
			}
		}
		return $tree;
	}

	public function add_categotry($data){
		return $this->db->insert(self::TBL_CATE)
	}

	public function get_cate($cat_id){
		$condition[]
	}
}