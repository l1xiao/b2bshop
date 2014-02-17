<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//前台首页控制器
class Home extends Home_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('goods_model');
		$this->load->library('cart');
	}

	public function index($cat_id = 0) {


		#获取购物车数量数据
		$data['carts'] = $this->cart->total_items();
		#获取商品数据
		$data['goods'] = $this->goods_model->get_allGoods($cat_id);
		$this->load->view('index.html', $data);
	}

	public function search() {
		#获取购物车数据
		$goods_name = $this->input->post('search');
		$data['cart'] = $this->cart->total_items();
		$data['goods'] = $this->goods_model->search($goods_name);
		var_dump($data['goods']);
		$this->load->view('index.html', $data);
	}

}