<?php
class User extends AppModel{
	public $name = 'User';
	public $useTable = 'Users';
	public $primaryKey = 'UserID';
	public function bindProfileImage(){
		$this->bindModel(array('hasOne'=>array('ProfileImage'=>array('className'=>'ProfileImage', 'foreignKey'=>'UserID', 'conditions'=>array('ProfileImage.Enabled'=>1)))));
	}
	public function bindProfileThumbnail($size){
		$this->bindModel(array('hasOne'=>array('ProfileImageThumbnail'=>array('className'=>'ProfileImageThumbnail', 'foreignKey'=>'UserID', 'conditions'=>array('ProfileImageThumbnail.Enabled'=>1, 'ProfileImageThumbnail.Width'=>(int)$size, 'ProfileImageThumbnail.Height'=>(int)$size )))));
	}
	public function unbindProfileThumbnail(){
		$this->unbindModel(array('hasOne'=>array('ProfileImageThumbnail')));
	}
	public function login($username, $password){		
		$username = strtolower($username);
		$data = $this->find('first', array('conditions'=>array('Username'=>$username, 'Enabled'=>1)));
		if(count($data) >= 1){
			$hashed = Security::loginHash($username, $password, $data['User']['Salt']);
			if($data['User']['Password'] == $hashed){
				return $data['User'];
			}
		}
		return 0;
	}
	public function changePassword($username, $oldPassword, $newPassword){
		$username = strtolower($username);
		$data = $this->find('first', array('conditions'=>array('Username'=>$username, 'Enabled'=>1)));
		
		if(count($data) >= 1){
			$hashed = Security::loginHash($username, $oldPassword, $data['User']['Salt']);
			
			if($data['User']['Password'] == $hashed){
				$hashed = Security::loginHash($username, $newPassword, $data['User']['Salt']);
				$this->create();
				$this->id = $data['User']['UserID'];
				$this->set('Password',$hashed);
				$this->save();
				return $this->getAffectedRows();
			}else{
				return 'wrongpass';
			}
		}
		return 0;
	}
	public function overridePassword($username, $password){	
		$data = $this->find('first', array('conditions'=>array('Username'=>$username)));
		
		if($data != 0){
			$hashed = Security::loginHash(strtolower($data['User']['Username']), $password, $data['User']['Salt']);
			$this->create();
			$this->id = $data['User']['UserID'];
			$this->set('Password', $hashed);
			$this->save();
			return $this->getAffectedRows();			
		}else{
			return 'wrongID';
		}
	}
	public function createUser($username, $email, $password){
		$usernameExists = $this->find('count', array('conditions'=>array('Username'=>$username)));	
		$emailExists = $this->find('count', array('conditions'=>array('Email'=>$email)));		
		if($usernameExists == 1){	
			return "username_exists";
		}else if($emailExists == 1){
			return "email_exists";
		}else{
			$datetime = CakeTime::sqlDatetime();
			$salt = Security::generateAuthKey();
			$hashed = Security::loginHash(strtolower($username), $password, $salt);
			$saveData = array('Username'=>$username, 'Password'=>$hashed, 'Email'=>$email, 'Salt'=>$salt, 'DateJoined'=>$datetime, 'RemoteAddress'=>$_SERVER['REMOTE_ADDR']);
			$success = $this->save($saveData);
			return $this->getInsertID();
		}
	}
	public function checkUsernameAvailable($username){		
		$usernameExists = $this->find('count', array('conditions'=>array('Username'=>$username)));	
		if($usernameExists == 0){
			return 1;
		}else{
			return 0;
		}
	}
}
?>