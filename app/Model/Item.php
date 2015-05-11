<?php
class Item extends AppModel{
	public $name = 'Item';
	public $useTable = 'Items';
	public $primaryKey = 'ItemID';
	//public $belongsTo = array('UploadSession'=>array('className'=>'UploadSession', 'foreignKey'=>'UploadSessionID'));
	public function bindImages(){
		$this->bindModel(array('hasMany'=>array('ItemImage'=>array('className'=>'ItemImage', 'foreignKey'=>'ItemID')))); //,'conditions'=>array("`ItemImage`.`DateCreated`=`Item`.`Updated`")
	}
	public function bindComments(){
		$this->bindModel(array('hasMany'=>array('ItemComment'=>array('className'=>'ItemComment', 'foreignKey'=>'ItemID', 'order'=>array('ItemComment.DateCreated DESC'), 'conditions'=>array('ItemComment.Deleted'=>0)))));
	}
	/*public function bindUploadSession(){
		$this->bindModel(array('belongsTo'=>array('UploadSession'=>array('className'=>'UploadSession', 'foreignKey'=>'UploadSessionID'))));
	}*/
	public function bindUser(){		
		$this->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User', 'foreignKey'=>'UserID'))));
	}
	public function bindItemComments(){
		$this->bindModel(array('hasMany'=>array('ItemComments'=>array('className'=>'ItemComments', 'foreignKey'=>'ItemID'))));
	}
	public function bindPreviewImage(){
		$this->bindModel(array('hasMany'=>array('PreviewImage'=>array('className'=>'PreviewImage', 'foreignKey'=>'ItemID', 'order'=>array('PreviewImage.PreviewImageID DESC')))));
	}
	public function bindRevisions(){
		$this->bindModel(array('hasMany'=>array('ItemRevision'=>array('className'=>'ItemRevision', 'foreignKey'=>'ItemID'))));
	}
	public function loadItemData($id){
		//$this->
	}
}