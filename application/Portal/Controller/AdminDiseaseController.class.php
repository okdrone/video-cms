<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Wanbo.ge <gewanbo@gmail.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminDiseaseController extends AdminbaseController {
    
	protected $disease_model;
	
	function _initialize() {
		parent::_initialize();
		$this->disease_model = D("Portal/Disease");
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists();
		$this->display();
	}
	
	// 文章添加
	public function add(){
		$this->display();
	}
	
	// 文章添加提交
	public function add_disease(){
		if (IS_POST) {

			$article=I("post.dise");

			$result=$this->disease_model->add($article);
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

		$ques=$this->disease_model->where("id=$id")->find();
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
			$result=$this->disease_model->save($article);
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
		
		$depart=I('request.departments');
		if(!empty($depart)){
		    $where=array('departments' => $depart);
		}

        $keyword=I('request.keyword');
        if(!empty($keyword)){
            $where['disease']=array('like',"%$keyword%");
        }

		$this->disease_model
		->alias("a")
		->where($where);

		
		$count=$this->disease_model->count();
			
		$page = $this->page($count, 20);

		$this->disease_model
		->alias("a")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.id DESC,departments");

		$posts=$this->disease_model->select();

        $departments = $this->disease_model->field('distinct departments')->select();
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
        $this->assign("departments",$departments);
	}
	
	// 文章删除
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->disease_model->where(array('id'=>$id))->save(array('status'=>3)) !==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			
			if ($this->disease_model->where(array('id'=>array('in',$ids)))->save(array('status'=>3))!==false) {
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
			$status=$this->disease_model->where(array("id"=>array('in',$ids),'status'=>3))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->disease_model->where(array("id"=>$id,'status'=>3))->delete();
				
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
			if ($this->disease_model->where(array("id"=>$id,'post_status'=>3))->save(array("post_status"=>"1"))) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}

	public function get_departments()
    {
        $data = array();

        $keyWord = trim($_GET['term']);
        if(!empty($keyWord)){
            $data = $this->disease_model->field('id, departments as `value`')->where("departments like '%" . $keyWord . "%'")->group('`value`')->select();
        }

        echo json_encode($data);
    }
}