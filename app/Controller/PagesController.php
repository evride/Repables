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

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('EmailEntry', 'Item');

	public function index(){
		$this->set('showBanner', true);
		$this->explore();
		
	}
	public function signup(){
		$this->autoRender = false;
		
		$exists = false;
		$success = false;
		$error = false;
		
		if($this->request->data('email')){
			$email = $this->request->data('email');
			
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$num = $this->EmailEntry->find('count', array('conditions'=>array('email'=>$email)));
			
				if($num >= 1){
					$exists = true;
				}else{
					$this->EmailEntry->save(array('email'=>$email));
					if($this->EmailEntry->getAffectedRows() == 1){
						$success = true;
					}else{
						$error = true;
					}
				}
				if($this->request->data('j')){
					if($this->request->data('j') == true){
						if($success){
							echo 'success';
						}else if($exists){
							echo 'exists';
						}else{
							echo 'error';
						}
						exit;
					}
				}
			}
		}
		$this->set('success', $success);
		$this->set('exists', $exists);
		$this->set('error', $error);
		$this->render('/Pages/signup', '');
	}
	public function licenseagreement(){
		
	}
	public function privacypolicy(){
		
	}
	public function dashboard(){
		
	}
	public function feedback(){
		$this->set('pageTitle', 'Send Us Feedback');
		if(strlen($this->request->data('Feedback.message')) >= 2){
			$this->loadModel('Feedback');
			$userID = 0;
			if($this->Session->check('User.ID')){
				$userID = $this->Session->read('User.ID');
			}
			$this->Feedback->save(array('UserID'=>$userID, 'VisitorID'=>$this->Cookie->read('Visitor.ID'), 'Comment'=>$this->request->data('Feedback.message'), 'RemoteAddress'=>$_SERVER['REMOTE_ADDR']));
			$this->set('jsRedirect', Router::url('/', true));
			$this->set('pageTitle', 'Thank You For Your Feedback');
			$this->set('confirmationMessage', 'Thank you for your feedback.');
			$this->render('action_confirmation');
		}
	}
	private function explore(){
		
		$this->set('pageTitle', 'Explore');
		$count = $this->Item->find('count', array('conditions'=>array('Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		$this->Item->bindPreviewImage();
		$this->Item->bindUser();
		$items = $this->Item->find('all', array('order'=>'Item.ItemID DESC', 'limit'=>24, 'recursive'=>2, 'conditions'=>array('Item.Published'=>1, 'Item.Processed'=>1, 'Item.Deleted'=>0, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		$this->set('page', 1);
		$this->set('totalPages', 1 );
		$this->set('homePage', 1);
		$this->set('items', $items);
		$this->render('/Explore/tag');
	}
}
