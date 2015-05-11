<?php
error_reporting(E_ALL);
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
App::uses('Controller', 'Controller');
App::uses('String', 'Utility');
App::uses('LoginSession', 'Model');
App::uses('Component', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $uses = array('LoginSession', 'User', 'VisitorSession');
	public $components = array('Login', 'Session', 'Cookie'=>array('name'=>'RepablesCookie', 'time'=>1314000, 'secure'=>false, 'key'=>"FAB+Ji|aDi:)5lnPAo@q'NHn='|w/<9$6~&a2IdR=8'uUms0b8y2tEKZ_oZ2bsA"));
	public function beforeFilter(){
		$this->Cookie->type('rijndael');
		
		if(!$this->Cookie->check('Visitor.ID')){
			$visitorUUID = String::uuid();
			$this->Cookie->write('Visitor.UUID', $visitorUUID);
			$this->Cookie->write('Visitor.OriginalVisit', time());
			$this->VisitorSession->save(array('UUID'=>$visitorUUID, 'RemoteAddress'=>$_SERVER['REMOTE_ADDR']));
			$this->Cookie->write('Visitor.ID', $this->VisitorSession->getInsertID());
			
		}else if($this->Cookie->check('User.ID')){			
			if(!$this->Session->check('User.ID')){
				$count = $this->LoginSession->find('count', array('conditions'=>array('Hash'=>$this->Cookie->read('Session.Hash'), 'UserID'=>$this->Cookie->read('User.ID'), 'Timestamp'=>$this->Cookie->read('Session.Timestamp'))));
				if($count == 1){
					$userData = $this->User->find('first', array('conditions'=>array('User.UserID'=>$this->Cookie->read('User.ID'))));
					$this->Login->writeSessionData($userData['User'], false);
				}				
			}
		}
		if($this->Session->check('User.Username')){
			$this->set('username', $this->Session->read('User.Username'));
			$this->set('userID', $this->Session->read('User.ID'));
		}else{
			$this->set('userID', -1);
		}
		if(defined('MAINTENANCE_MODE')){
			if(MAINTENANCE_MODE == 1){
				if($this->Session->read('User.Administrator') == 0){
					
					if(!($this->params['controller'] == "user" && ($this->action == "login" || $this->action == "logout"))){
					
						$this->autoRender = false;
						$this->set('confirmationMessage', 'Repables is being updated. We\'ll be back in a minute.');
						$this->render('/Pages/action_confirmation');
						$this->response->send();
						$this->_stop();
					}
				}
			}
		}
		$this->set('adminlevel', $this->Session->read('User.Administrator'));
	}
	
}
