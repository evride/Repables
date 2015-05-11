<?php
class GalleryImage extends AppModel{
	public $name = 'UploadSession';
	public $useTable = 'UploadSessions';
	public $primaryKey = 'UploadSessionID';
	public $hasMany = array(
		'Upload'=> array(	'className'	=>	'Upload',
							'foreignKey'=>	'UploadSessionID',
							'conditions'=>	array('Upload.Deleted'=>0)
						)
					);
	public function getSessionID($uploadSession, $visitorSession, $userID){
		$retID = -1;
		$this->unbindModel(
			array('hasMany' => array('Upload'))
		);
		$orConditional = array('UploadSession.VisitorID'=>$visitorSession);
		if($userID >= 1){
			$orConditional['UploadSession.UserID'] = $userID;
		}
		$data = $this->find('first', array('fields'=>array('UploadSession.UploadSessionID'), 'conditions'=>array('UploadSession.UUID'=>$uploadSession, 'OR'=>$orConditional)));
		
		if(isset($data['UploadSession'])){
			$retID = $data['UploadSession']['UploadSessionID'];
		}
		
		$this->bindModel(
			array('hasMany' => array('Upload'))
		);
		return $retID;
	}
}