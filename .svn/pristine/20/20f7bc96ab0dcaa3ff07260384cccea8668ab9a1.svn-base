<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#权限控制器
class Privilege extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->helper('captcha');
		$this->load->library('form_validation');
	}

	public function login(){
		$this->load->view('login.html');
	}

	#生成验证码
	public function code(){
		#调用函数生成验证码
		$vals = array(
			'word_length' => 4,
		);
		#这里直接生成了图片
		$code = create_captcha($vals);
		#将验证码字符串保存到session中
		$this->session->set_userdata('code',$code);
	}


	#处理登录
	public function signin(){
		#设置验证规则
		$this->form_validation->set_rules('username','用户名','required');
		$this->form_validation->set_rules('password','密码','required');

		#获取表单数据
		$captcha = strtolower($this->input->post('captcha'));

		#获取session中保存的验证码
		$code = strtolower($this->session->userdata('code'));

		if ($captcha === $code){
			#验证码正确，则需要验证用户名和密码
			if ($this->form_validation->run() == false){
				$data['message'] = validation_errors();
				$data['url'] = site_url('admin/privilege/login');
				$data['wait'] = 3;
				$this->load->view('message.html',$data);
			} else{
				// $username = $this->input->post('username',true);
				// $password = $this->input->post('password',true);
				// #if($username == 'admin' && $password == '123')
				// $this->db->where('admin_name', $username);
				// $this->db->where('password', $password);
				// $query = $this->db->get('ci_admin');
				$this->load->model('login_model');
				$query = $this->login_model->admin_validate();
				if($query){
					# OK，保存session信息,然后跳转到首页
					$this->session->set_userdata('admin',1);
					redirect('admin/main/index');
				} else{
					# error
					$data['url'] = site_url('admin/privilege/login');
					$data['message'] = '用户名和密码错误，请重新填写';
					$data['wait'] = 3;
					$this->load->view('message.html',$data);
				}
			}
			
		} else {
			#验证码不正确，给出提示页面，然后返回
			$data['url'] = site_url('admin/privilege/login');
			$data['message'] = '验证码错误，请重新填写';
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		}
	}

	public function logout(){
		$this->session->unset_userdata('admin');
		$this->session->sess_destroy();
		redirect('admin/privilege/login');
	}
}