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
    
	protected $doctor_model;
	
	function _initialize() {
		parent::_initialize();
		$this->doctor_model = D("Portal/Doctor");
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
	public function add_doctor(){
		if (IS_POST) {

		    if(!empty($_POST['doct']['doctor'])){
		        $_POST['doct']['name'] = $_POST['doct']['doctor'];
		        unset($_POST['doct']['doctor']);
            } else {
                $this->erorr("The Doctor field does not be empty!");
            }

		    if(!empty($_POST['doct']['city'])){
		        $arr = explode('-', $_POST['doct']['city']);
		        if(count($arr) > 1) {
                    $_POST['doct']['province'] = $arr[0];
                    $_POST['doct']['city'] = $arr[1];
                } else {
                    $this->erorr("The City field is wrong!");
                }
            } else {
		        $this->erorr("The City field does not be empty!");
            }

            $_POST['doct']['add_time']=time();
            $_POST['doct']['last_modified']=time();
			$_POST['doct']['author']=get_current_admin_id();
			$article=I("post.doct");

			$result=$this->doctor_model->add($article);
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

		$doct=$this->doctor_model->where("id=$id")->find();
		$doct['city'] = $doct['province'] . '-' . $doct['city'];
		$this->assign("doct",$doct);
		$this->display();
	}
	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {
			$post_id=intval($_POST['doct']['id']);

            if(!empty($_POST['doct']['doctor'])){
                $_POST['doct']['name'] = $_POST['doct']['doctor'];
                unset($_POST['doct']['doctor']);
            } else {
                $this->erorr("The Doctor field does not be empty!");
            }

            if(!empty($_POST['doct']['city'])){
                $arr = explode('-', $_POST['doct']['city']);
                if(count($arr) > 1) {
                    $_POST['doct']['province'] = $arr[0];
                    $_POST['doct']['city'] = $arr[1];
                } else {
                    $this->erorr("The City field is wrong!");
                }
            } else {
                $this->erorr("The City field does not be empty!");
            }

			unset($_POST['doct']['author']);
			$_POST['doct']['last_modified']=time();
			$article=I("post.doct");

			$result=$this->doctor_model->where('id=' . $post_id)->save($article);
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

		$locate=I('request.locate');
		if(!empty($locate)){
		    $locateArr = explode('-', $locate);
		    if(count($locateArr) == 2) {
                $where['province'] = array(
                    array('EQ', trim($locateArr[0]))
                );
                $where['city'] = array(
                    array('EQ', trim($locateArr[1]))
                );
            }
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['name']=array('like',"%$keyword%");
		}
			
		$this->doctor_model
		->alias("a")
		->where($where);

		
		$count=$this->doctor_model->count();
			
		$page = $this->page($count, 20);
			
		$this->doctor_model
		->alias("a")
		->join("__USERS__ c ON a.author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("a.add_time DESC");
		if(empty($term_id)){
		    $this->doctor_model->field('a.*,c.user_login,c.user_nicename');
		}else{
		    $this->doctor_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
		    $this->doctor_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
		}
		$posts=$this->doctor_model->select();
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
	}
	
	// 文章删除
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->doctor_model->where(array('id'=>$id))->save(array('status'=>3)) !==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			
			if ($this->doctor_model->where(array('id'=>array('in',$ids)))->save(array('status'=>3))!==false) {
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
			$status=$this->doctor_model->where(array("id"=>array('in',$ids),'status'=>3))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->doctor_model->where(array("id"=>$id,'status'=>3))->delete();
				
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
			if ($this->doctor_model->where(array("id"=>$id,'post_status'=>3))->save(array("post_status"=>"1"))) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}
	
}