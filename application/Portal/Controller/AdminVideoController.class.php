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

class AdminVideoController extends AdminbaseController {
    
	protected $video_model;
	protected $doctor_model;
	protected $disease_model;
    protected $question_model;
	
	function _initialize() {
		parent::_initialize();
		$this->video_model = D("Portal/Video");
		$this->disease_model = D("Portal/Disease");
		$this->doctor_model = D("Portal/Doctor");
        $this->question_model = D("Portal/Question");
	}
	
	// 后台文章管理列表
	public function index(){
		$this->_lists(array("v.status"=>array('neq',3)));
		//$this->_getTree();
		$this->display();
	}
	
	// 文章添加
	public function add(){
	    $city = $this->_getCity();
		$departments=$this->_getDepartments();
		$questions=$this->_getQuestions();
        $videos=$this->_getVideos();
		$this->assign("city",$city);
        $this->assign("departments",$departments);
        $this->assign("questions",$questions);
        $this->assign("videos",$videos);
		$this->display();
	}
	
	// 文章添加提交
	public function add_video(){
		if (IS_POST) {

		    $video = array();

		    $video['doctor'] = $_POST['video']['doctor'];
            $video['disease'] = $_POST['video']['disease'];

            if(empty($_POST['video']['title'])) {
                $this->error("请输入标题！");
            }

            $video['title'] = $_POST['video']['title'];
            $video['keywords'] = $_POST['video']['keywords'];
            $video['abstract'] = htmlspecialchars_decode($_POST['video']['desc']);

            $video['video_time'] = $_POST['video']['time'];

            if(empty($_POST['video']['link'])){
                $this->error("请输入视频地址！");
            }

            $video['video_link'] = $_POST['video']['link'];

			if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}
			$_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $video['smeta'] = json_encode($_POST['smeta']);

            if(isset($_POST['questions']) && is_array($_POST['questions'])){
                $video['questions'] = implode(',', array_filter($_POST['questions'], function($item) { $item = trim($item); return is_numeric($item); }));
            }

            if(isset($_POST['recommend']) && is_array($_POST['recommend'])){
                $video['recommend'] = implode(',', array_filter($_POST['recommend'], function($item) { $item = trim($item); return is_numeric($item); }));
            }

            $video['status'] = $_POST['video']['status'];
			$video['add_time'] = time();
            $video['last_modified'] = time();
            $video['pub_time'] = strtotime($_POST['video']['pub_time']);
            $video['author'] = get_current_admin_id();

			$result=$this->video_model->add($video);
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

        $city = $this->_getCity();
        $departments=$this->_getDepartments();
        $questions=$this->_getQuestions();
        $videos=$this->_getVideos();

        $video=$this->video_model->where("id=$id")->find();

        $tempQues = array();
        if(!empty($video['questions'])){
            foreach(explode(',', $video['questions']) as $item){
                foreach($questions as &$ques){
                    if($ques['id'] == $item){
                        $tempQues[] = $ques;
                        $ques['checked'] = 'checked';
                    }
                }
            }
        }

        $video['questions'] = $tempQues;

        $tempRecommend = array();
        if(!empty($video['recommend'])){
            foreach(explode(',', $video['recommend']) as $item){
                foreach($videos as &$vid){
                    if($vid['id'] == $item){
                        $tempRecommend[] = $vid;
                        $vid['checked'] = 'checked';
                    }
                }
            }
        }

        $video['recommend'] = $tempRecommend;

        $this->assign("video",$video);
        $this->assign("smeta",json_decode($video['smeta'],true));

        $this->assign("city",$city);
        $this->assign("departments",$departments);
        $this->assign("questions",$questions);
        $this->assign("videos",$videos);
        $this->display();
	}
	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {
            $video_id=intval($_POST['video']['id']);

            if(empty($video_id)){
                $this->error("要编辑的内容不存在！");
            }

            $video = array();

            $video['doctor'] = $_POST['video']['doctor'];
            $video['disease'] = $_POST['video']['disease'];

            if(empty($_POST['video']['title'])) {
                $this->error("请输入标题！");
            }

            $video['title'] = $_POST['video']['title'];
            $video['keywords'] = $_POST['video']['keywords'];
            $video['abstract'] = htmlspecialchars_decode($_POST['video']['desc']);

            $video['video_time'] = $_POST['video']['time'];

            if(empty($_POST['video']['link'])){
                $this->error("请输入视频地址！");
            }

            $video['video_link'] = $_POST['video']['link'];

            if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
                foreach ($_POST['photos_url'] as $key=>$url){
                    $photourl=sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $video['smeta'] = json_encode($_POST['smeta']);

            if(isset($_POST['questions']) && is_array($_POST['questions'])){
                $video['questions'] = implode(',', array_filter($_POST['questions'], function($item) { $item = trim($item); return is_numeric($item); }));
            }

            if(isset($_POST['recommend']) && is_array($_POST['recommend'])){
                $video['recommend'] = implode(',', array_filter($_POST['recommend'], function($item) { $item = trim($item); return is_numeric($item); }));
            }

            $video['status'] = $_POST['video']['status'];
            $video['last_modified'] = time();
            $video['pub_time'] = strtotime($_POST['video']['pub_time']);

			$result=$this->video_model->where('id='.$video_id)->save($video);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	// 文章排序
	public function listorders() {
		$status = parent::_listorders($this->doctor_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 * 文章列表处理方法,根据不同条件显示不同的列表
	 * @param array $where 查询条件
	 */
	private function _lists($where=array()){
		$term_id=I('request.term',0,'intval');
		
		//$where['post_type']=array(array('eq',1),array('exp','IS NULL'),'OR');
		
		$start_time=I('request.start_time');
		if(!empty($start_time)){
		    $where['post_date']=array(
		        array('EGT',$start_time)
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
		    if(empty($where['post_date'])){
		        $where['post_date']=array();
		    }
		    array_push($where['post_date'], array('ELT',$end_time));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
		    $where['title']=array('like',"%$keyword%");
		}
			
		$this->video_model
		->alias("v")
		->where($where);
		
		$count=$this->video_model->count();

		$page = $this->page($count, 20);
			
		$this->video_model
		->alias("v")
		->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
        ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
        ->join("__USERS__ c ON v.author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("v.last_modified DESC");

        $this->video_model->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name,c.user_login,c.user_nicename');
		$posts=$this->video_model->select();

		//dump($this->video_model->getLastSql());
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("posts",$posts);
	}

	private function _getCity(){
        return $this->doctor_model->field('province, city')->group('province, city')->select();
    }

    private function _getDepartments(){
        return $this->disease_model->field('distinct departments')->select();
    }

    private function _getQuestions(){
        return $this->question_model->field('id, title')->where('status=1')->select();
    }

    private function _getVideos(){
        return $this->video_model->field('id, title')->where('status=1')->select();
    }
	
	// 文章删除
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->video_model->where(array('id'=>$id))->save(array('post_status'=>3)) !==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			
			if ($this->video_model->where(array('id'=>array('in',$ids)))->save(array('post_status'=>3))!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	
	// 文章审核
	public function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
		    $ids = I('post.ids/a');
			
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('post_status'=>1)) !== false ) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('post_status'=>0)) !== false) {
				$this->success("取消审核成功！");
			} else {
				$this->error("取消审核失败！");
			}
		}
	}
	
	// 文章置顶
	public function top(){
		if(isset($_POST['ids']) && $_GET["top"]){
			$ids = I('post.ids/a');
			
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>1))!==false) {
				$this->success("置顶成功！");
			} else {
				$this->error("置顶失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["untop"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('istop'=>0))!==false) {
				$this->success("取消置顶成功！");
			} else {
				$this->error("取消置顶失败！");
			}
		}
	}
	
	// 文章推荐
	public function recommend(){
		if(isset($_POST['ids']) && $_GET["recommend"]){
			$ids = I('post.ids/a');
			
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>1))!==false) {
				$this->success("推荐成功！");
			} else {
				$this->error("推荐失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unrecommend"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->video_model->where(array('id'=>array('in',$ids)))->save(array('recommended'=>0))!==false) {
				$this->success("取消推荐成功！");
			} else {
				$this->error("取消推荐失败！");
			}
		}
	}
	
	// 文章批量移动
	public function move(){
		if(IS_POST){
			if(isset($_GET['ids']) && $_GET['old_term_id'] && isset($_POST['term_id'])){
			    $old_term_id=I('get.old_term_id',0,'intval');
			    $term_id=I('post.term_id',0,'intval');
			    if($old_term_id!=$term_id){
			        $ids=explode(',', I('get.ids/s'));
			        $ids=array_map('intval', $ids);
			         
			        foreach ($ids as $id){
			            $this->doctor_model->where(array('object_id'=>$id,'term_id'=>$old_term_id))->delete();
			            $find_relation_count=$this->doctor_model->where(array('object_id'=>$id,'term_id'=>$term_id))->count();
			            if($find_relation_count==0){
			                $this->doctor_model->add(array('object_id'=>$id,'term_id'=>$term_id));
			            }
			        }
			        
			    }
			    
			    $this->success("移动成功！");
			}
		}else{
			$tree = new \Tree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$terms = $this->disease_model->order(array("path"=>"ASC"))->select();
			$new_terms=array();
			foreach ($terms as $r) {
				$r['id']=$r['term_id'];
				$r['parentid']=$r['parent'];
				$new_terms[] = $r;
			}
			$tree->init($new_terms);
			$tree_tpl="<option value='\$id'>\$spacer\$name</option>";
			$tree=$tree->get_tree(0,$tree_tpl);
			 
			$this->assign("terms_tree",$tree);
			$this->display();
		}
	}
	
	// 文章批量复制
	public function copy(){
	    if(IS_POST){
	        if(isset($_GET['ids']) && isset($_POST['term_id'])){
	            $ids=explode(',', I('get.ids/s'));
	            $ids=array_map('intval', $ids);
	            $uid=sp_get_current_admin_id();
	            $term_id=I('post.term_id',0,'intval');
	            $term_count=$terms_model=M('Terms')->where(array('term_id'=>$term_id))->count();
	            if($term_count==0){
	                $this->error('分类不存在！');
	            }
	            
	            $data=array();
	            
	            foreach ($ids as $id){
	                $find_post=$this->video_model->field('post_keywords,post_source,post_content,post_title,post_excerpt,smeta')->where(array('id'=>$id))->find();
	                if($find_post){
	                    $find_post['post_author']=$uid;
	                    $find_post['post_date']=date('Y-m-d H:i:s');
	                    $find_post['post_modified']=date('Y-m-d H:i:s');
	                    $post_id=$this->video_model->add($find_post);
	                    if($post_id>0){
	                        array_push($data, array('object_id'=>$post_id,'term_id'=>$term_id));
	                    }
	                }
	            }
	            
	            if ( $this->doctor_model->addAll($data) !== false) {
	                $this->success("复制成功！");
	            } else {
	                $this->error("复制失败！");
	            }
	        }
	    }else{
	        $tree = new \Tree();
	        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
	        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
	        $terms = $this->disease_model->order(array("path"=>"ASC"))->select();
	        $new_terms=array();
	        foreach ($terms as $r) {
	            $r['id']=$r['term_id'];
	            $r['parentid']=$r['parent'];
	            $new_terms[] = $r;
	        }
	        $tree->init($new_terms);
	        $tree_tpl="<option value='\$id'>\$spacer\$name</option>";
	        $tree=$tree->get_tree(0,$tree_tpl);
	
	        $this->assign("terms_tree",$tree);
	        $this->display();
	    }
	}
	
	// 文章回收站列表
	public function recyclebin(){
		$this->_lists(array('post_status'=>array('eq',3)));
		$this->_getTree();
		$this->display();
	}
	
	// 清除已经删除的文章
	public function clean(){
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			$ids = array_map('intval', $ids);
			$status=$this->video_model->where(array("id"=>array('in',$ids),'post_status'=>3))->delete();
			$this->doctor_model->where(array('object_id'=>array('in',$ids)))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->video_model->where(array("id"=>$id,'post_status'=>3))->delete();
				$this->doctor_model->where(array('object_id'=>$id))->delete();
				
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
			if ($this->video_model->where(array("id"=>$id,'post_status'=>3))->save(array("post_status"=>"1"))) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}

	public function api_get_hospital(){
        $ret = array('code' => -1, 'data' => array());
	    if(isset($_GET['c'])){
	        $temp = explode('-', $_GET['c']);
            $temp = array_filter($temp, trim);
            if(count($temp) > 1){
                $province = $temp[0];
                $city = $temp[1];

                $data = $this->doctor_model->field('distinct hospital')->where(array('province' => $province, 'city' => $city))->select();

                $ret['data'] = array_map(array_shift, $data);
            }
        }
        echo json_encode($ret);
    }

    public function api_get_doctor(){
        $ret = array('code' => -1, 'data' => array());
        if(isset($_GET['c']) && isset($_GET['h'])){
            $temp = explode('-', $_GET['c']);
            $temp = array_filter($temp, trim);
            if(count($temp) > 1){
                $province = $temp[0];
                $city = $temp[1];
                $hospital = trim($_GET['h']);

                $ret['data'] = $this->doctor_model->field('id, name')->where(array('province' => $province, 'city' => $city, 'hospital' => $hospital))->group('name')->select();
            }
        }
        echo json_encode($ret);
    }

    public function api_get_disease(){
        $ret = array('code' => -1, 'data' => array());
        if(isset($_GET['d'])){
            $departments = trim($_GET['d']);
            $ret['data'] = $this->disease_model->field('id, disease')->where(array('departments' => $departments))->group('disease')->select();
        }
        echo json_encode($ret);
    }
}