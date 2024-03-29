<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//商品控制器

class Goods extends Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('goodstype_model');
		$this->load->model('attribute_model');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->model('goods_model');
		$this->load->library('pagination');

	}

	#默认显示的首页是商品列表
	public function index($offset = ''){
		#配置分页信息
		$config['base_url'] = site_url('admin/goods/index/');
		$config['total_rows'] = $this->goods_model->count_goods();
		$config['per_page'] = 15;
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
		$data['goods'] = $this->goods_model->list_goods($limit,$offset); 
		$this->load->view('goods_list.html',$data);

	}


	#添加商品页面
	public function add(){
		#获取所有的商品类型信息
		$data['goodstypes'] = $this->goodstype_model->get_all_types();
		#获取分类信息
		$data['cates'] = $this->category_model->list_cate();
		#获取品牌信息 
		$data['brands'] = $this->brand_model->list_brands();
		$this->load->view('goods_add.html',$data);
	}

	#编辑一条商品
	public function edit(){
		#获取所有的商品类型信息
		$data['goodstypes'] = $this->goodstype_model->get_all_types();
		#获取分类信息
		$data['cates'] = $this->category_model->list_cate();
		#获取品牌信息 
		$data['brands'] = $this->brand_model->list_brands();
		#获取商品信息
		$goods_id = $this->uri->segment(4,0);
		$data['goods'] = $this->goods_model->get_goods($goods_id);
		#获取商品属性,并构造对应的html值
		$type_id = $data['goods']['type_id'];
		$attrs = $this->goods_model->get_attrs($goods_id,$type_id);

		$html = '';
		foreach ($attrs as $v) {
			$html .= "<tr>";
			$html .= "<td class='label'>".$v['attr_name']."</td>";
			$html .="<td>";
			$html .= "<input type='hidden' name='attr_id_list[]' value='".$v['attr_id']."'>";
			switch ($v['attr_input_type']) {
				case 0:
					# 文本框
					$html .= "<input name='attr_value_list[]'' type='text' size='40' value='".$v['default_value']."'>";
					break;
				case 1:
					# 下拉列表
					$arr = explode(PHP_EOL, $v['attr_value']);
					$html .= "<select name='attr_value_list[]'>";
					$html .= "<option value=''>请选择...</option>";
					foreach ($arr as $val) {
						$html .= "<option value='$val'";
						if ($val == $v['default_value']) {
							$html .= "selected";
						}
						$html .= ">$val</option>";
					}								  
					$html .= "</select>";
					break;
				case 2:
					# 文本域
					break;
				
				default:
					# code...
					break;
			}

			$html .="</td>";
			$html .="</tr>";
		}
		$data['attr_list'] = $html;

		$this->load->view('goods_edit.html',$data);
	}
	
	#把添加的商品插入到数据库
	public function insert(){
		#获取提交的数据
		$data['goods_name'] = $this->input->post('goods_name');
		$data['goods_sn'] = $this->input->post('goods_sn');
		$data['cat_id'] = $this->input->post('cat_id');
		$data['brand_id'] = $this->input->post('brand_id');
		$data['market_price'] = $this->input->post('market_price');
		$data['shop_price'] = $this->input->post('shop_price');
		$data['promote_price'] = $this->input->post('promote_price');
		$data['promote_start_time'] = strtotime($this->input->post('goods_name'));
		$data['promote_end_time'] = strtotime($this->input->post('goods_name'));
		$data['goods_number'] = $this->input->post('goods_number');
		$data['goods_brief'] = $this->input->post('goods_brief');
		$data['is_best'] = $this->input->post('is_best');
		$data['is_new'] = $this->input->post('is_new');
		$data['is_hot'] = $this->input->post('is_hot');
		$data['is_onsale'] = $this->input->post('is_onsale');

		#设置上传图片参数
		$config['upload_path'] = './public/uploads/';
		$config['allowed_types'] = 'jpg|gif|png';
		$config['max_size'] = 500;
		$this->load->library('upload',$config);
	    $this->upload->initialize($config);

	    #判断是否上传了图片,图像处理是否正确
		if ($this->upload->do_multi_upload('goods_img')) {			
			# 上传成功
			
			#获取所有上传的图片信息
			$pictures = $this->upload->get_multi_upload_data();
			#添加商品
			if ($goods_id = $this->goods_model->add_goods($data)) {
				# 添加商品成功,获取属性并插入到商品属性关联表中
				$attr_ids = $this->input->post('attr_id_list');
				$attr_values = $this->input->post('attr_value_list');
				#属性插入
				if(!empty($attr_values)){
					foreach ($attr_values as $k => $v) {
						if (!empty($v)) {
							$data2['goods_id'] = $goods_id;
							$data2['attr_id'] = $attr_ids[$k];
							$data2['attr_value'] = $v;
							$this->db->insert('goods_attr',$data2);
						}
					}	
				}
				#图片插入
				foreach ($pictures as $v) {
					$data1['goods_id'] = $goods_id;
					$data1['pic_name'] = $v['file_name'];
					$data1['file_ext'] = $v['file_ext'];
					$this->db->insert('goods_pic',$data1);
				}
				$data['message'] = '添加商品成功';
				$data['url'] = site_url('admin/goods/index');
				$data['wait'] = 3;
				$this->load->view('message.html',$data);
			} else {
				# 失败
				$data['message'] = '添加商品失败';
				$data['url'] = site_url('admin/goods/add');
				$data['wait'] = 3;
				$this->load->view('message.html',$data);
			}				
		} else {
			# 上传失败
			$data['message'] = $this->upload->display_errors();
			$data['url'] = site_url('admin/goods/add');
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		}
			
			
		
	
	}

	#修改一条商品数据
	public function update(){
		#获取提交的数据
		$data['goods_id'] = $this->input->post('goods_id');
		$goods_id = $this->input->post('goods_id');
		$data['goods_name'] = $this->input->post('goods_name');
		$data['cat_id'] = $this->input->post('cat_id');
		$data['brand_id'] = $this->input->post('brand_id');
		$data['market_price'] = $this->input->post('market_price');
		$data['shop_price'] = $this->input->post('shop_price');
		$data['promote_price'] = $this->input->post('promote_price');
		$data['promote_start_time'] = strtotime($this->input->post('goods_name'));
		$data['promote_end_time'] = strtotime($this->input->post('goods_name'));
		$data['goods_number'] = $this->input->post('goods_number');
		$data['goods_brief'] = $this->input->post('goods_brief');
		$data['is_best'] = $this->input->post('is_best');
		$data['is_new'] = $this->input->post('is_new');
		$data['is_hot'] = $this->input->post('is_hot');
		$data['is_onsale'] = $this->input->post('is_onsale');
		#配置上传图片参数
		$config['upload_path'] = './public/uploads/';
		$config['allowed_types'] = 'jpg|gif|png';
		$config['max_size'] = 500;
		$this->load->library('upload',$config);
	    $this->upload->initialize($config);
		
		#判断是否上传了图片,图像处理是否正确
		if ($this->upload->do_multi_upload('goods_img')) {			
			# 上传成功
			
			#获取所有上传的图片信息
			$pictures = $this->upload->get_multi_upload_data();
			#添加商品
			if ($goods_id = $this->goods_model->update_goods($data)) {
				# 添加商品成功,获取属性并插入到商品属性关联表中
				$attr_ids = $this->input->post('attr_id_list');
				$attr_values = $this->input->post('attr_value_list');
				#属性插入
				if(!empty($attr_values)){
					foreach ($attr_values as $k => $v) {
						if (!empty($v)) {
							$data2['goods_id'] = $goods_id;
							$data2['attr_id'] = $attr_ids[$k];
							$data2['attr_value'] = $v;
							$this->db->insert('goods_attr',$data2);
						}
					}	
				}
				#图片插入
				foreach ($pictures as $v) {
					$data1['goods_id'] = $goods_id;
					$data1['pic_name'] = $v['file_name'];
					$data1['file_ext'] = $v['file_ext'];
					$this->db->insert('goods_pic',$data1);
				}
				$data['message'] = '修改商品成功'.$data1['pic_name'];
				$data['url'] = site_url('admin/goods/index');
				$data['wait'] = 3;
				$this->load->view('message.html',$data);
			} else {
				# 失败
				$data['message'] = '添加商品失败';
				$data['url'] = site_url("dmin/goods/edit/$goods_id");
				$data['wait'] = 3;
				$this->load->view('message.html',$data);
			}				
		} else {
			# 上传失败
			$data['message'] = $this->upload->display_errors();
			$data['url'] = site_url("admin/goods/edit/$goods_id");
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		}
		
	}
			
	public function delete_goods($goods_id) {
		
		$condition['goods_id'] = $goods_id;
		if($this->db->where($condition)->delete('ci_goods')) {
			$data['message'] = '删除商品成功';
			$data['url'] = site_url("dmin/goods/list");
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '删除失败';
			$data['url'] = site_url("admin/goods/list");
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		}

	}

		

	public function comments_list($offset = ''){
		#配置分页信息
		$config['base_url'] = site_url('admin/goods/comments_list/');
		$config['total_rows'] = $this->goods_model->count_comments();
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
		$data['comments_list'] = $this->goods_model->list_comments($limit,$offset);
		$this->load->view('comments_list.html',$data);
	}

	public function comments_detail($comments_id){
		$data['comments'] = $this->goods_model->get_comments($comments_id);
		$this->load->view('comments_reply.html',$data);
	}

	public function comments_delete($comments_id){
		if($this->goods_model->comments_delete($comments_id)){
			$data['message'] = '隐藏评论成功';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '隐藏评论失败';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 5;
			$this->load->view('message.html',$data);
		} 
		
	}

	public function comments_show($comments_id){
		if($this->goods_model->comments_show($comments_id)){
			$data['message'] = '显示评论成功';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '显示评论失败';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		} 
		
	}

	public function comments_reply($comments_id){
		$comments = $this->input->post('admin_reply');
		
		if($this->goods_model->reply_comments($comments_id,$comments)){
			$data['message'] = '回复成功';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 2;
			$this->load->view('message.html',$data);
		} else {
			$data['message'] = '回复失败';
			$data['url'] = site_url('admin/goods/comments_list');
			$data['wait'] = 3;
			$this->load->view('message.html',$data);
		} 
	}

	public function create_attrs_html(){
		#获取类型id
		$type_id = $this->input->get('type_id');
		// echo $type_id;
		$attrs = $this->attribute_model->get_attrs($type_id);
		#根据获取到的属性值构造html字符串

		$html = '';
		foreach ($attrs as $v) {
			$html .= "<tr>";
			$html .= "<td class='label'>".$v['attr_name']."</td>";
			$html .="<td>";
			$html .= "<input type='hidden' name='attr_id_list[]' value='".$v['attr_id']."'>";
			switch ($v['attr_input_type']) {
				case 0:
					# 文本框
					$html .= "<input name='attr_value_list[]'' type='text' size='40'>";
					break;
				case 1:
					# 下拉列表
					$arr = explode(PHP_EOL, $v['attr_value']);
					$html .= "<select name='attr_value_list[]'>";
					$html .= "<option value=''>请选择...</option>";
					foreach ($arr as $v) {
						$html .= "<option value='$v'>$v</option>";
					}								  
					$html .= "</select>";
					break;
				case 2:
					# 文本域
					break;
				
				default:
					# code...
					break;
			}

			$html .="</td>";
			$html .="</tr>";
		}

		echo $html;
	}

	#图片展示页面
	public function photo($goods_id) {
		$data['pictures'] = $this->goods_model->get_picId($goods_id);
		$this->load->view('photo.html',$data);
	}

	#缩略图片
	public function get_thumb($goods_pic_id) {
		#如果数据库有该图，获得该图的名字
		var_dump($goods_pic_id);
		$pic_name = $this->goods_model->get_picByid($goods_pic_id);
		if ($pic_name) {
			$data['goods_img'] = $pic_name[0];
			$config_img['source_image'] = "./public/uploads/" . $pic_name[0]['pic_name'];
			$config_img['create_thumb'] = true;
			$config_img['maintain_ratio'] = true;
			$config_img['width'] = 230;
			$config_img['height'] = 230;
			echo '<pre>';
			// var_dump($pic_name);
			// var_dump($data);
			$name = explode('.', $data['goods_img']['pic_name']);
			$pic_name = $name[0];
			// var_dump($pic_name);
			// // var_dump($config_img);
			// echo $data['goods_img']['file_ext'];
			// exit();
			#载入并初始化图像处理类
			$this->load->library('image_lib',$config_img);
			$goods = $this->goods_model->get_goodsId($goods_pic_id);
			$goods_id = $goods['goods_id'];
			if ($this->image_lib->resize()) {
				# 缩略ok,得到缩略图的名称
				$thumb = $pic_name . $this->image_lib->thumb_marker. $data['goods_img']['file_ext'];
				var_dump($thumb);
				// exit();
				$this->goods_model->thumb($goods_pic_id,$thumb);
				$data['message'] = "缩略成功！admin/goods/photo/$goods_id";
				$data['url'] = site_url("admin/goods/photo/$goods_id");
				$data['wait'] = 2;
				$this->load->view('message.html',$data);
			} else {
				# 缩略失败
				$data['message'] = $this->image_lib->display_errors();
				$data['url'] = site_url("admin/goods/photo/$goods_id");
				$data['wait'] = 2;
				$this->load->view('message.html',$data);
			}
		}
	}

	#状态修改：展示优先级
	public function photo_state() {
		$pic_usage = $this->input->post('pic_usage');
		$pic_goods_id = $this->input->post('goods_pic_id');
		$pic_description = $this->input->post('pic_description');
		$result = $this->goods_model->photo_state($pic_goods_id, $pic_usage, $pic_description);
		$goods = $this->goods_model->get_goodsId($pic_goods_id);
		$goods_id = $goods['goods_id'];
		if ($result) {
				$data['message'] = $goods_id;
				$data['url'] = site_url("admin/goods/photo/$goods_id");
				$data['wait'] = 2;
				$this->load->view('message.html',$data);
			} else {
				$data['message'] = "更改失败！";
				$data['url'] = site_url("admin/goods/photo/$goods_id");
				$data['wait'] = 2;
				$this->load->view('message.html',$data);
			}

	}


	#删除图片
	public function delete_pic($goods_pic_id) {
		// $goods_pic_id = $this->input->post('goods_pic_id');
		$goods = $this->goods_model->get_goodsId($goods_pic_id);
		$goods_id = $goods['goods_id'];
		if($this->goods_model->delete_pic($goods_pic_id)) {
			$data['message'] = '删除成功';
			$data['url'] = site_url("admin/goods/photo/$goods_id");
			$data['wait'] = 2;
			$this->load->view('message.html',$data);	
		} else {
			$data['message'] = $this->image_lib->display_errors();
			$data['url'] = site_url("admin/goods/photo/$goods_id");
			$data['wait'] = 2;
			$this->load->view('message.html',$data);	
		}
	}
}