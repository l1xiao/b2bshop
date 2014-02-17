<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//商品控制器
class Goods extends Home_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('goods_model');
		$this->load->model('attribute_model');
	}

	public function index($goods_id){
		$data['best'] = $this->goods_model->get_bestgoods(5);
		foreach ($data['best'] as $k => $v) {
			$data['best'][$k]['pic'] = $this->goods_model->get_picId($v['goods_id']);
		}
		// // $data['best'] = $this->goods_model->
		$data['goods'] = $this->goods_model->get_goods($goods_id);
		// 获取评论
		$data['comments'] = $this->goods_model->get_comments_by_goods_id($goods_id);
		// 获取属性
		$data['goods_attr'] = $this->attribute_model->get_goods_attrs($goods_id);
		// 获取图片名称
		$data['goods_pic'] = $this->goods_model->get_picId($goods_id);
		// echo '<pre>';
		// var_dump($data);
		$this->load->view('detail.html',$data);
		
	}
}