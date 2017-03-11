<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminDoctorController extends AdminbaseController {
    
	protected $question_model;
	
	function _initialize() {
		parent::_initialize();
		$this->question_model = D("Portal/Doctor");
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists(array("status"=>array('neq',3)));
		$this->display();
	}
	
	// 文章添加
	public function add(){
		$this->display();
	}
	
	// 文章添加提交
	public function add_question(){
		if (IS_POST) {
			if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}
			$_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $_POST['ques']['add_time']=time();
            $_POST['ques']['last_modified']=time();
			$_POST['ques']['author']=get_current_admin_id();
			$article=I("post.ques");
            $article['smeta']=json_encode($_POST['smeta']);

			//dump($article);

			$result=$this->question_model->add($article);
			if ($result) {
				$this->success("添加成功！");
			} else {
				$this->error("添加失败！");
			}
			 
		}
	}
	
	// 文章编辑
	public function edit(){
		$id=  I("get.id",0,'intval');

		$ques=$this->question_model->where("id=$id")->find();
		$this->assign("ques",$ques);
		$this->assign("smeta",json_decode($ques['smeta'],true));
		$this->display();
	}
	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {
			$post_id=intval($_POST['ques']['id']);
			
			if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}
			$_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
			unset($_POST['ques']['author']);
			$_POST['ques']['last_modified']=time();
			$article=I("post.ques");
			$article['smeta']=json_encode($_POST['smeta']);
			$result=$this->question_model->save($article);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	/**
	 * 文章列表处理方法,根据不同条件显示不同的列表
	 * @param array $where 查询条件
	 */
	private function _lists($where=array()){

		$start_time=I('request.start_time');
		if(!empty($start_time)){
		    $where['add_time']=array(
		        array('EGT',strtotime($start_time))
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
		    if(empty($where['add_time'])){
		        $where['add_time']=array();
		    }
		    array_push($where['add_time'], array('ELT',strtotime($end_time)));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['title']=array('like',"%$keyword%");
		}
			
		$this->question_model
		->alias("a")
		->where($where);

		
		$count=$this->question_model->count();
			
		$page = $this->page($count, 20);
			
		$this->question_model
		->alias("a")
		->join("__USERS__ c ON a.author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.add_time DESC");
		if(empty($term_id)){
		    $this->question_model->field('a.*,c.user_login,c.user_nicename');
		}else{
		    $this->question_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
		    $this->question_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
		}
		$posts=$this->question_model->select();
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
	}
	
	// 文章删除
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->question_model->where(array('id'=>$id))->save(array('status'=>3)) !==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			
			if ($this->question_model->where(array('id'=>array('in',$ids)))->save(array('status'=>3))!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	
	// 文章回收站列表
	public function recyclebin(){
		$this->_lists(array('status'=>array('eq',3)));
		$this->display();
	}
	
	// 清除已经删除的文章
	public function clean(){
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			$ids = array_map('intval', $ids);
			$status=$this->question_model->where(array("id"=>array('in',$ids),'status'=>3))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->question_model->where(array("id"=>$id,'status'=>3))->delete();
				
				if ($status!==false) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}
	
	// 文章还原
	public function restore(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->question_model->where(array("id"=>$id,'post_status'=>3))->save(array("post_status"=>"1"))) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}
	
}