<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('Sanitize', 'Utility');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ProfilesController extends AppController {

	public $name = 'Profiles';
	public $usernameRegex = "/^[A-Za-z0-9_]*?[A-Za-z]+[A-Za-z0-9_]*$/";
	
	public $uses = array('User', 'LoginSession', 'ProfileImage', 'ProfileImageThumbnail', 'Item');
	
	public function index($username, $page = 0){
		if(isset($username) && strlen($username)>=2){
			$user = $this->User->find('first', array('conditions'=>array('User.Username'=>$username)));
			if(isset($user['User']['UserID']) && $user['User']['UserID'] >= 1) {
				$this->set('UserData', $user);
				
				$count = $this->Item->find('count', array('conditions'=>array('Item.UserID'=>$user['User']['UserID'], 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
				$this->Item->bindPreviewImage();
				$this->Item->bindUser();
				$page = (int)$page;
				$limit = 24;
				if($page < 1){
					$page = 1;
				}
				$items = $this->Item->find('all', array('order'=>'Item.ItemID DESC', 'offset'=> ($page-1) * $limit, 'limit'=>$limit, 'recursive'=>2, 'conditions'=>array('Item.UserID'=>$user['User']['UserID'], 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
				$this->set('page', $page);
				$this->set('totalPages', ceil($count / $limit ) );
				$this->set('items', $items);
				$this->render('/Profiles/index');
			}else{
				$this->render('/Errors/ItemNotFound');
			}
		}else{
			$this->render('/Errors/ItemNotFound');
		}
	}
}
