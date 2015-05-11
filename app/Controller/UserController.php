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
App::uses('CakeEmail', 'Network/Email');
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UserController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'User';
	public $usernameRegex = "/^[A-Za-z0-9_]*?[A-Za-z]+[A-Za-z0-9_]*$/";
	
	public $uses = array('User', 'LoginSession', 'ProfileImage', 'ProfileImageThumbnail');
	public function beforeFilter(){
		parent::beforeFilter();
		if(!$this->Session->check('User.ID')){
			if($this->action != "resetpassword" && $this->action != 'checkname' && $this->action != "login" && $this->action != "logout" && $this->action != "register"){
				$this->setAction('login');
			}
		}
		$this->set('sectionTitle', 'User');
	}
	
	public function login(){
		if($this->Session->check('User.ID')){
			$this->Session->destroy();
		}	
		
		$redirect = '/';
		if(count($this->request->params['pass'])>=1){
			$redirect .= implode('/', $this->request->params['pass']);
		}
		$this->set('pageTitle', 'Login');
		$this->set('loginFormURL', $this->request->here);
		$username = Sanitize::escape($this->request->data('User.username'));
		$password = Sanitize::escape($this->request->data('User.password'));
		$this->_login($username, $password, $redirect);
		/*if(strlen($username) >= 1 && strlen($password) >= 1){
			$users = $this->User->login($username, $password);
			if($users != 0){
				$this->Session->write('User.ID', $users['UserID']);
				$this->Session->write('User.Username', $users['Username']);
				$this->Session->write('User.Fullname', $users['Fullname']);
				
				
				$sessionHash = String::uuid();
				$sessionTimestamp = $this->CakeTime->sqlDatetime();
				$this->Cookie->write('User.ID', $users['UserID']);
				$this->Cookie->write('Session.Hash', $sessionHash);
				$this->Cookie->write('Session.Timestamp', $sessionTimestamp);
				
				$sessionData = array('UserID'=>$users['UserID'], 'Hash'=>$sessionHash, 'Timestamp'=>$sessionTimestamp, 'RemoteAddress'=>$_SERVER['REMOTE_ADDRESS']);
				$this->LoginSession->save($sessionData);
				
				
				$this->redirect(array('controller'=>'Dashboard', 'action'=>'index'));
			}else{
				$this->set('ErrorFlashText', "Username or password was incorrect.");
			}
		}*/
	}
	private function _login($username, $password, $redirect = '/'){
		if(strlen($username) >= 1 && strlen($password)>=1){
			$result = $this->Login->login($username, $password);
			
			switch($result){
				case 1:				
					$this->redirect($redirect);
					break;
				default:
					$this->set('ErrorFlashText', "Username or password was incorrect.");
					break;
			}
		}
	}
	public function forcepasswrd($username, $password){
		if($this->Session->read('User.Administrator') == '1'){
			$this->autoRender = false;
			$this->User->overridePassword($username, $password);
		}
	}
	public function resetpassword($username = null, $uuid = null){
	
		$this->loadModel('ResetRequest');
		$this->set('pageTitle', 'Reset Your Password');
		if(isset($username) && isset($uuid) && is_string($username) && is_string($uuid) && strlen($username)>=1 && strlen($uuid)>=24){
			$this->ResetRequest->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User', 'foreignKey'=>'UserID'))));
			$resetReq = $this->ResetRequest->find('first', array('conditions'=>array('User.Username'=>$username, 'ResetRequest.UUID'=>$uuid, '`ResetRequest.RequestDate`>=\'' . CakeTime::sqlDatetime('-10 Days') . "'")));
			if($this->Session->check('User.ID')){
				$this->ResetRequest->delete($resetReq['ResetRequest']['ResetRequestID']);
				$this->redirect('/logout');
			}
			if(isset($resetReq['ResetRequest']['ResetRequestID']) && $resetReq['ResetRequest']['ResetRequestID'] >= 1){
				$this->set('ResetUsername', $username);
				$this->set('ResetUUID', $uuid);
				if($this->request->data('User.save') == 1){
					if($this->request->data('User.newpassword') == $this->request->data('User.newpassword2')){
						if(strlen($this->request->data('User.newpassword')) >= 6){
							$this->User->overridePassword($username, $this->request->data('User.newpassword'));
							$this->ResetRequest->delete($resetReq['ResetRequest']['ResetRequestID']);
							$this->_login($username, $this->request->data('User.newpassword'));
						}else{
							$this->set('UserSettingWarning', 'Password is not long enough!');
						}
					}else{
						$this->set('UserSettingWarning', 'The passwords do not match!');
					}
				}
				$this->render('resetconfirm');
			}
		}else if($this->request->data('User.save') == 1){
			$userStr = $this->request->data('User.username');
			if(filter_var($userStr, FILTER_VALIDATE_EMAIL)){
				$user = $this->User->find('first', array('conditions'=>array('User.Email'=>$this->request->data('User.username'))));				
			}else if(preg_match($this->usernameRegex, $userStr)){				
				$user = $this->User->find('first', array('conditions'=>array('User.Username'=>$this->request->data('User.username'))));
			}
			if(isset($user['User']['UserID']) && $user['User']['UserID'] >= 1){
				
				$uuid = String::uuid();
				$this->ResetRequest->save(array('UserID'=>$user['User']['UserID'], 'UUID'=>$uuid, 'RequestDate'=>CakeTime::sqlDatetime()));
				$requestID = $this->ResetRequest->getInsertID();
				
				
				$email = new CakeEmail();
				$name = $user['User']['Username'];
				if(strlen($user['User']['Fullname'])){
					$name = $user['User']['Fullname'];
				}
				$resetURL = Router::url('/resetpassword/' . $user['User']['Username'] . '/' . $uuid . '/', true);
				$email->viewVars(array('name'=>$name, 'username'=>$user['User']['Username'], 'resetURL'=>$resetURL));
				$email->template('resetpassword');
				$email->emailFormat('html');
				$email->from(array('no-reply@repables.com'=>'Repables'));
				$email->to($user['User']['Email'], $name);
				$email->subject('Repables Account Password Reset Request');
				$email->send();
			}
		}		
	}
	public function logout(){
		$this->Session->destroy();	
		$this->Cookie->delete('User.ID');
		$this->Cookie->delete('Session.Hash');
		$this->Cookie->delete('Session.Timestamp');
		$this->redirect(array('controller'=>'User', 'action'=>'login'));
	}
	
	
	public function register(){
		$jsIncludes = array('register');
		$this->set('jsIncludes', $jsIncludes);
		
		$this->set('pageTitle', 'Register');
		if($this->request->data('User.saved') == 1){
			$username = Sanitize::escape($this->request->data('User.username'));
			
			if(preg_match($this->usernameRegex, $username)){
				if(strtolower($username) != "anonymous"){
					$email = Sanitize::escape($this->request->data('User.email'));
					$password = Sanitize::escape($this->request->data('User.password'));
					$confirm = Sanitize::escape($this->request->data('User.confirmpassword'));
					if(strlen($username) >= 2){
						if(filter_var($email, FILTER_VALIDATE_EMAIL)){
							if(strlen($password) >= 6){
								if($password == $confirm){
									if($this->request->data('User.termsagree') == 1){
										$success = $this->User->createUser($username, $email, $password);
										if($success >= 1){
											//$this->redirect(array('controller'=>'Users', 'action'=>'index'));
											$this->_login($username, $password);
										}else if($success == 'exists'){
											$this->Session->setFlash('User already exists!');
										}else{
											$this->set('ErrorFlashText', 'Failure experienced while creating user');
										}
									}else{
										$this->set('ErrorFlashText', "You must agree to the Terms of Service and Privacy Policy to create an account.");
									}
								}else{	
									$this->set('ErrorFlashText', "Passwords entered did not match.");
								}
							}else{
								$this->set('ErrorFlashText', "Password is not long enough.");
							}
						}else{
							$this->set('ErrorFlashText', "Insert a valid email address.");
						}
					}else{
						$this->set('ErrorFlashText', 'Username is not long enough');
					}
				}else{
					$this->set('ErrorFlashText', "Sorry, you can't be \"anonymous\".");
				}
			}else{
				$this->set('ErrorFlashText', "Only alpha-numeric (and underscore) characters allowed in usernames.");
			}
		}
	}
	public function checkname(){
		$this->autoRender = false;
		$username = Sanitize::escape($this->request->data('User.username'));
		if(strlen($username)>= 1){
			if(preg_match($this->usernameRegex, $username)){
				echo $this->User->checkUsernameAvailable($username);
			}else{
				echo 'invalid';
			}
		}else{
			echo 0;
		}
	}
	
	public function settings(){
		
		$this->Image = $this->Components->load('Image');
		$cssIncludes = array('jquery-ui-1.10.4.custom.min');
		$this->set('jsIncludes', array('jquery-ui-1.10.4.custom.min', 'jquery.iframe-transport', 'jquery.fileupload', 'user_settings'));
		$this->set('cssIncludes', $cssIncludes);
		
		
		$this->set('pageTitle', 'User Settings');
		$userID = $this->Session->read('User.ID');
		if($this->request->data('User.save') == 1){		
			if($this->request->data('User.imageID') >= 1){
				$data = $this->ProfileImage->find('first', array('conditions'=>array('ProfileImage.ProfileImageID'=>$this->request->data('User.imageID'))));
				$file = $data['ProfileImage']['File'];
				
				$fileExt = substr($file, strrpos($file, '.'));
				
				$saveMD5 = md5(String::uuid());
				$medSaveLoc = PROFILE_IMAGE_DIR . DS . 'thumbnails' . DS . $userID . '_' . $saveMD5 . '_120w_120h_' . $fileExt;
				$smallSaveLoc = PROFILE_IMAGE_DIR . DS . 'thumbnails' . DS . $userID . '_' . $saveMD5 . '_50w_50h_' . $fileExt;
				
				
				$this->Image->createProfileThumbnail($file, $medSaveLoc, (int)$this->request->data('User.thumbnailX'), (int)$this->request->data('User.thumbnailY'), (int)$this->request->data('User.thumbnailSize'), 120);
				
				$this->Image->createProfileThumbnail($file, $smallSaveLoc, (int)$this->request->data('User.thumbnailX'), (int)$this->request->data('User.thumbnailY'), (int)$this->request->data('User.thumbnailSize'), 50); 
				
				$this->ProfileImage->updateAll(array('ProfileImage.Enabled'=>0), array('ProfileImage.UserID'=>$userID));
				
				$this->ProfileImage->read(null, (int)$this->request->data('User.imageID'));
				$this->ProfileImage->id = (int)$this->request->data('User.imageID');
				$this->ProfileImage->set('Enabled', 1);
				$this->ProfileImage->save();
				
				$this->ProfileImageThumbnail->updateAll(array('ProfileImageThumbnail.Enabled'=>0), array('ProfileImageThumbnail.UserID'=>$userID));
				
				$this->ProfileImageThumbnail->saveMany(array(array('UserID'=>$userID, 
						'ProfileImageID'=>(int)$this->request->data('User.imageID'),
						'File'=>$medSaveLoc,
						'Width'=>120,
						'Height'=>120,
						'Enabled'=>1,
						'DateCreated'=>CakeTime::sqlDatetime()
						),
						array('UserID'=>$userID, 
						'ProfileImageID'=>(int)$this->request->data('User.imageID'),
						'File'=>$smallSaveLoc,
						'Width'=>50,
						'Height'=>50,
						'Enabled'=>1,
						'DateCreated'=>CakeTime::sqlDatetime()
						)));				
			}
			
			$this->User->read(null, $userID);
			$this->User->id = $userID;
			$this->User->set(array(
				'Fullname'=>Sanitize::clean($this->request->data('User.name')),
				'Location'=>Sanitize::clean($this->request->data('User.location')),
				'Company'=>Sanitize::clean($this->request->data('User.company')),
				'Bio'=>Sanitize::clean($this->request->data('User.bio')),
				'Email'=>Sanitize::clean($this->request->data('User.email')),
				'EmailPublic'=>(bool)$this->request->data('User.displayemail'),
				'Birthdate'=> (int)$this->request->data('User.birthdate.year') . '-' . (int)$this->request->data('User.birthdate.month') . '-'. (int)$this->request->data('User.birthdate.day'),
				'DisplayBirthdate'=>(bool)$this->request->data('User.displayemail')?'birthdate':'no',
				'HideInappropriate'=>(bool)$this->request->data('User.hideinappropriate'),
				'Website'=>Sanitize::clean($this->request->data('User.website'))
				)
			);
			$this->User->save();
			$this->Session->write('User.ShowInappropriate', (bool)$this->request->data('User.hideinappropriate')==0);
			
			$this->set('pageTitle', 'Settings Saved');
			$this->set('jsRedirect', Router::url('/User/settings', true));
			$this->set('confirmationMessage', 'Your settings have been saved.');
			
			$this->render('/Pages/action_confirmation');
		}
		
		
		$data = $this->User->find('first', array('conditions'=>array('User.UserID'=>$userID)));
		$data = $data['User'];
		$profileImage = $this->ProfileImage->find('first', array('conditions'=>array('ProfileImage.UserID'=>$userID, 'ProfileImage.Enabled'=>1)));
		
		$data['hasProfileImage'] = false;
		if(isset($profileImage['ProfileImage']['File'])){
			$data['hasProfileImage'] = true;
			$data['profileImage'] = $profileImage['ProfileImage'];
		}
		//$data['Birthdate'] = array('year'=>1989, 'month'=>7, 'day'=>17);
		if($data['Birthdate'] == '0000-00-00'){
			$data['Birthdate'] = date('Y-m-d', time());
		}
		$birthdate = DateTime::createFromFormat('Y-m-d', $data['Birthdate'], new DateTimezone('America/New_York'));
		$data['Birthdate'] = array('year'=>$birthdate->format('Y'), 'month'=>$birthdate->format('m'), 'day'=>$birthdate->format('d'));
		$data['age'] = $birthdate->diff(new DateTime('now', new DateTimezone('America/New_York')))->y;
		//'name'=>$data['Fullname'], 'location'=>$data['Location'], 'company'=>$data['Company'], 'bio'=>$data['Bio'], 'email'=>$data['Email'], 'website'=>$data['Website']
		$this->set($data); //array('data'=>$data, 'hasProfileImage'=>$hasProfileImage, 'profileImage'=>$profileImage));
		
	}
	public function image(){
		$this->Image = $this->Components->load('Image');
		$this->autoRender = false;
		$retData = array('status'=>'fail');
		if($this->Session->check('User.ID')){
			if(isset($_FILES['data']['tmp_name']['User']['image'])){
				$userID = $this->Session->read('User.ID');
				
				$tempImg = $_FILES['data']['tmp_name']['User']['image'];
				$img = $_FILES['data']['name']['User']['image'];
				
				$fileExt = substr($img, strrpos($img, '.'));
				$fileName = $userID . '_' . md5(String::uuid()) . $fileExt;
				$fileLoc = PROFILE_IMAGE_DIR . DS . $fileName;
				move_uploaded_file($tempImg, $fileLoc);
				chmod($fileLoc, 0744);
				
				$userImage = $this->Image->loadImage($fileLoc);
				
				$this->ProfileImage->save(array('UserID'=>$userID, 'File'=>$fileLoc, 'Width'=>imagesx($userImage), 'Height'=>imagesy($userImage), 'RemoteAddress'=>$_SERVER['REMOTE_ADDR'], 'DateUploaded'=>CakeTime::sqlDatetime()));
				
				$imageID = $this->ProfileImage->getInsertID();
				$fileURL = PROFILE_IMAGE_DIR . DS . $fileName;
				$retData['id'] = $imageID;
				$retData['image'] = Router::url(DS . $fileURL, true);
				$retData['status'] = 'success';
			}
			
		}
		$this->set('data', $retData);
		$this->render('/General/SerializeJson/', '');
	}
	public function changepassword(){
		$this->set('pageTitle', 'Change Your Password');
		if($this->request->data('User.save') == 1){
			if($this->Session->check('User.ID')){
				if($this->request->data('User.password') != $this->request->data('User.newpassword')){
					if($this->request->data('User.newpassword') == $this->request->data('User.newpassword2')){
						if(strlen($this->request->data('User.newpassword')) >= 6){
							$result = $this->User->changePassword($this->Session->read('User.Username'), $this->request->data('User.password'), $this->request->data('User.newpassword'));
							if($result == 1){							
								$this->set('UserSettingSuccess', 'Your password was successfully changed.');
							}else if($result == "wrongpass"){
								$this->set('UserSettingWarning', 'The password entered was incorrect.');
							}else{
								$this->set('UserSettingWarning', "Something didn't work, sorry.");
							}
						}
					}else{
						$this->set('UserSettingWarning', 'The new password did not match.');					
					}
				}else{
					$this->set('UserSettingWarning', 'The new password matches the old password.');	
				}
			}else{
				$this->set('UserSettingWarning', "Uh, dude... you're not logged in.");
			}		
		}
	}
}
