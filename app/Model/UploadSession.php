<?php
class UploadSession extends AppModel{
	public $name = 'UploadSession';
	public $useTable = 'UploadSessions';
	public $primaryKey = 'UploadSessionID';
	public $hasAndBelongsToMany = array(
		'Upload'=> array(	'className'	=>	'Upload',
							'joinTable'=>'Uploads_UploadSessions',
							'foreignKey'=>	'UploadSessionID',
							'associationForeignKey'=> 'UploadID',
							'conditions'=>	array('Upload.Deleted'=>0)
						)
					);
	public function bindUser(){		
		$this->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User', 'foreignKey'=>'UserID'))));
	}
	public function getSessionID($uploadSession, $visitorSession, $userID){
		$retID = -1;
		$this->unbindModel(
			array('hasAndBelongsToMany' => array('Upload'))
		);
		$data = $this->find('first', array('fields'=>array('UploadSession.UploadSessionID'), 'conditions'=>array('UploadSession.UUID'=>$uploadSession, 'UploadSession.UserID'=>$userID)));
		
		if(isset($data['UploadSession'])){
			$retID = $data['UploadSession']['UploadSessionID'];
		}
		
		$this->bindModel(
			array('hasAndBelongsToMany' => array('Upload'))
		);
		return $retID;
	}
}