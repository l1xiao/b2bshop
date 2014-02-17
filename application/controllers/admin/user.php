<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//用户控制器

class User extends Admin_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('user_model');
		$this->load->library('pagination');

	}

	#默认显示的首页是商品列表
	public function index($offset = '') {
		#配置分页信息
		$config['base_url'] = site_url('admin/user/index/');
		$config['total_rows'] = $this->user_model->count_user();
		$config['per_page'] = 2;
		$config['uri_segment'] = 4;

		#自定义分页链接
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';

		#初始化分页类
		$this->pagination->initialize($config);
		
		#生成分页信息
		$data['pageinfo'] = $this->pagination->create_links();
		$limit = $config['per_page'];
		$data['user'] = $this->user_model->list_user($limit,$offset); 
		$this->load->view('user_list.html', $data);
	}

	public function user_comments($offset = '') {
		#配置分页信息
		$config['base_url'] = site_url('admin/user/user_comments/');
		$config['total_rows'] = $this->user_model->count_comments();
		$config['per_page'] = 2;
		$config['uri_segment'] = 4;

		#自定义分页链接
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';

		#初始化分页类
		$this->pagination->initialize($config);
		
		#生成分页信息
		$data['pageinfo'] = $this->pagination->create_links();
		$limit = $config['per_page'];
		$data['user'] = $this->user_model->list_comments($limit,$offset); 
		$this->load->view('user_comments.html', $data);

	}

	public function comments_delete($comments_id){
		if($this->user_model->comments_delete($comments_id)){
			$data['message'] = '隐藏评论成功';
			$data['url'] = site_url('admin/user/user_comments');
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '隐藏评论失败';
			$data['url'] = site_url('admin/user/user_comments');
			$data['wait'] = 5;
			$this->load->view('message.html',$data);
		} 
	}
		


	public function comments_show($comments_id){
		if($this->user_model->comments_show($comments_id)){
			$data['message'] = '显示评论成功';
			$data['url'] = site_url('admin/user/user_comments');
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '显示评论失败';
			$data['url'] = site_url('admin/user/user_comments');
			$data['wait'] = 5;
			$this->load->view('message.html',$data);
		} 
	}
}
