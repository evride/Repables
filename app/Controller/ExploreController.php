<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');
App::uses('Security', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('CakeTime', 'Utility');
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ExploreController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Users';

	
	public $uses = array('User', 'UploadSession', 'LoginSession', 'Item');
	public $ignoreTerms = array('the', 'an', 'and');
	
	public function index($page = 1){
	
		$this->set('pageTitle', 'Explore');
		$count = $this->Item->find('count', array('conditions'=>array('Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		$this->Item->bindPreviewImage();
		$this->Item->bindUser();
		$page = (int)$page;
		$limit = 24;
		if($page < 1){
			$page = 1;
		}
		$items = $this->Item->find('all', array('order'=>'Item.ItemID DESC', 'offset'=> ($page-1) * $limit, 'limit'=>$limit, 'recursive'=>2, 'conditions'=>array('Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		$this->set('page', $page);
		$this->set('totalPages', ceil($count / $limit ) );
		$this->set('items', $items);
		$this->set('linkPrefix', '/explore/');
		$this->render('/Explore/tag');
	}
	/*public function search(){
		$this->Item->unbindAll();
		
		
	}
	public function search($t = "", $page=0){
		echo $this->request->data('search');
		if(strlen($this->request->data('search')) >= 1 && $t == ""){
			$this->redirect('/search/' . urlencode($this->request->data('search')) . '/');
		}else{
			$this->tag($t, $page, false);
		}
	}*/
	public function search($t = "", $page=1){
		if(strlen($this->request->data('search')) >= 1 && $t == ""){
			$this->redirect('/search/' . urlencode($this->request->data('search')) . '/');
		}else{
			if($t == "" || !is_numeric($page)){
				echo 'error';
			}else{		
				$limit = 24;
				$strict = false;
				if($page < 1){
					$page = 1;
				}
				$t = Sanitize::escape($t);
				$tagArray = array("Item.Search='" . $t ."'", "Item.Search LIKE '" . $t . ",%'", "Item.Search LIKE '%," . $t . "'", "Item.Search LIKE '%," . $t . ",%'");
				if(!$strict){
					$tagArray[] = "Item.Search LIKE '%" . $t . "%'";
				}
				
				$count = $this->Item->find('count', array('conditions'=>array('OR'=>$tagArray, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
			
				$this->Item->bindPreviewImage();
				
				$limit = 24;
				//$this->UploadSession->bindUser();
				$this->Item->bindUser();
				$items = $this->Item->find('all', array('conditions'=>array('OR'=>$tagArray, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate')), 'offset'=>($page-1) * $limit, 'limit'=>$limit, 'recursive'=>2));
				$this->set('tag', $t);
				$this->set('items', $items);
				$this->set('searchType', 'query');
				$this->set('linkPrefix', '/search/' . $t . '/');
				
				$this->set('page', $page);
				$this->set('totalPages', ceil($count / $limit ) );
				$this->set('items', $items);
				
				$this->render('/Explore/tag');
			}
		}
	}
	public function tag($t, $page=1, $strict = true){
		
		if($t == "" || !is_numeric($page)){
			echo 'error';
		}else{		
			$limit = 24;
			if($page < 1){
				$page = 1;
			}
			$t = Sanitize::escape($t);
			$tagArray = array("Item.Tags='" . $t ."'", "Item.Tags LIKE '" . $t . ",%'", "Item.Tags LIKE '%," . $t . "'", "Item.Tags LIKE '%," . $t . ",%'");
			if(!$strict){
				$tagArray[] = "Item.Tags LIKE '%" . $t . "%'";
			}
			
			$count = $this->Item->find('count', array('conditions'=>array('OR'=>$tagArray, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		
			$this->Item->bindPreviewImage();
			
			$limit = 24;
			//$this->UploadSession->bindUser();
			$this->Item->bindUser();
			$items = $this->Item->find('all', array('conditions'=>array('OR'=>$tagArray, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate')), 'offset'=>($page-1) * $limit, 'limit'=>$limit, 'recursive'=>2));
			$this->set('tag', $t);
			$this->set('items', $items);
			$this->set('searchType', 'tag');
			$this->set('linkPrefix', '/tag/' . $t . '/');
			
			$this->set('page', $page);
			$this->set('totalPages', ceil($count / $limit ) );
			$this->set('items', $items);
			
			$this->render('/Explore/tag');
		}
	}
	public function search2($query, $start = 0, $limit = 60){
		
		
		//$query = $this->request->data('query');		
		///$start = $this->request->data('start');
		//$limit = $this->request->data('limit');
		
		/*if(!isset($start)){
			$start = 0;
		}
		if(!isset($limit)){
			$limit = 60;
		}*/
		
		$terms = explode(' ', $query);
		$numbers = array();
		
		for($i = 0; $i < count($terms); $i++){
			if(is_numeric($terms[$i])){
				if((int)$terms[$i] == $terms[$i]){
					$numbers[] = (int)$terms[$i];
					$itemIDs[] = array('Item.ItemID'=>(int)$terms[$i]);
				}
			}
			if(strlen($terms[$i]) >= 1 && !in_array(strtolower($terms[$i]), $this->ignoreTerms)){
				$keywords[] = Sanitize::escape($terms[$i]);
			}			
		}
		
		
		$keywords = array_unique($keywords);
		$conditions[1][0] = array('OR'=>array());
		$conditions[1][1] = array();
		$conditions[2] = array();
		
		
		for($i = 0; $i < count($numbers); $i++){
			$conditions[1][0]['OR'][] = "Item.ItemID='" . $numbers[$i] . "'";
		}
		$keywordsQuery = array();
		for($i = 0; $i < count($keywords); $i++){		
			$tagsQuery[] = "Item.Tags LIKE '%". $keywords[$i] . "%'";
			$keywordsQuery[] = "Item.Description LIKE '%" . $keywords[$i] . "%'";
			$namesQuery[] = "Item.Name LIKE '%" . $keywords[$i] . "%'";
		}
		$keywordNumbers = array();
		
		$conditions[1][1] = $keywordsQuery;
		
		if(count($numbers)>=2){
			for($i = 0; $i < count($numbers); $i++){
				$keywordNumbers[] = array();
				for($e = 0; $e < count($numbers); $e++){
					if($i != $e){
						$keywordNumbers[$i][] = "Item.Description LIKE '%" . $numbers[$e] . "%'";
						$keywordNumbers[$i][] = "Item.Name LIKE '%" . $numbers[$e] . "%'";
					}
				}
			}
			for($i = 1; $i <=3; $i++){
				$conditions[$i][2]['OR'] = $keywordNumbers;
			}
		}
		$kn = array_merge($keywords, $numbers);
		for($i = 0; $i < count($kn); $i++){
			$conditions[2][] = "Item.Description LIKE '%" . $kn[$i] . "%'";
		}
		
		$conditions[0]['Item.Description'] = Sanitize::escape($query);
		
		
		for($i = 0; $i <count($conditions); $i++){
			$conditions[$i][] = "Item.Deleted='0'";
			$conditions[$i][] = "Item.Processed='1'";
			
		}
		
		$remainingLimit = $limit;
		
		$results = array();
		for($i = 0; $i < count($conditions) && count($results) < $limit; $i++){
			$result = $this->Item->find('all', array('conditions'=>$conditions[$i],'limit'=>$remainingLimit));	
			$results = array_merge($results, $result);
			
		}
		if(count($results)>$limit){
			$results = array_slice($results, 0, $limit);
		}
		$this->autoRender = false;
		print_r($results);
	}
	private function get_search(){
		$this->Item->unbindAll();
		
		$query = $this->request->data('query');		
		$start = $this->request->data('start');
		$limit = $this->request->data('limit');
		
		if(!isset($start)){
			$start = 0;
		}
		if(!isset($limit)){
			$limit = 60;
		}
		
		$conditions = array();//, array('OR'=>array()), array('OR'=>array()), array('AND'=>array()), array('OR'=>array()));
		
		
		for($i = 0; $i < count($terms); $i++){
			if(is_numeric($terms[$i])){
				if((int)$terms[$i] == $terms[$i]){
					$numbers[] = (int)$terms[$i];
					$itemIDs[] = array('Item.ItemID'=>(int)$terms[$i]);
				}
			}else{
				$keywords[] = Sanitize::escape($terms[$i]);
			}
		}
		$keywords = array_unique($keywords);
		
		$conditions[1][0] = array('OR'=>array());
		$conditions[2][0] = array('OR'=>array());
		$conditions[3][0] = array('OR'=>array());
		$conditions[1][1] = array();
		$conditions[2][1] = array();
		$conditions[3][1] = array();
		$conditions[4] = array();
		
		$keywordsQuery = array();
		
		for($i = 0; $i < count($keywords); $i++){
			$tagsQuery[] = "Item.Tags LIKE '%". $keywords[$i] . "%'";
			
			$keywordsQuery[] = "Item.Description LIKE '%" . $keywords[$i] . "%'";
			
		}
		
		for($i = 0; $i < count($conditions); $i++){
			$conditions[$i];
		}
		$result = $this->Item->find('all', array('conditions'=>array('OR'=>$itemIDs), 'offset'=>$start, 'limit'=>$remainingLimit));
		$results = array();
		for($i = 0; $i < count($conditions) && count($results) < $limit; $i++){
			$result = $this->find('all', array('conditions'=>$conditions[$i],'offset'=>$start, 'limit'=>$remainingLimit));	
			$results = array_merge($results, $result);
			
		}
	}
}
