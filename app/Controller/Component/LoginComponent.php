<?php

App::uses('Component', 'Controller');
class LoginComponent extends Component {
	
	private $_controller = null;
    public function initialize(Controller $controller) {
		$this->_controller = $controller;
    }

	public function login($username, $password){
		if($this->_controller->Session->check('User.ID')){
			$this->_controller->Session->destroy();
		}		
		if(strlen($username) >= 1 && strlen($password) >= 1){
			$data = $this->_controller->User->login($username, $password);
			if($data != 0){
				$this->writeSessionData($data);				
				return 1;
			}else{
				return 0;
			}
		}
	}
	public function writeSessionData($data, $addNewSession = true){
		$this->_controller->Session->write('User.ID', $data['UserID']);
		$this->_controller->Session->write('User.Username', $data['Username']);
		$this->_controller->Session->write('User.Fullname', $data['Fullname']);
		$this->_controller->Session->write('User.Administrator', $data['Administrator']);
		$this->_controller->Session->write('User.ShowInappropriate', $data['HideInappropriate'] == 0);
		
		if($addNewSession){
			$sessionHash = String::uuid();
			$sessionTimestamp = CakeTime::sqlDatetime();
			$this->_controller->Cookie->write('User.ID', $data['UserID']);
			$this->_controller->Cookie->write('Session.Hash', $sessionHash);
			$this->_controller->Cookie->write('Session.Timestamp', $sessionTimestamp);
					
			$sessionData = array('UserID'=>$data['UserID'], 'Hash'=>$sessionHash, 'Timestamp'=>$sessionTimestamp, 'RemoteAddress'=>$_SERVER['REMOTE_ADDR']);
			$this->_controller->LoginSession->save($sessionData);
		}
	}
}

?>