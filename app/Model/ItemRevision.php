<?php
class ItemRevision extends AppModel{
	public $name = 'ItemRevision';
	public $useTable = 'ItemRevisions';
	public $primaryKey = 'RevisionID';
	
	public function bindItemModel(){
		$this->bindModel(array("belongsTo"=>array("Item"=>array("foreignKey"=>"ItemID"))));
	}
	public function bindFiles(){
		$this->bindModel(array("hasMany"=>array("File"=>array("foreignKey"=>"RevisionID"))));
	}
}