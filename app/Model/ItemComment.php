<?php
class ItemComment extends AppModel{
	public $name = 'ItemComment';
	public $useTable = 'ItemComments';
	public $primaryKey = 'ItemCommentID';
	public $belongsTo = array('User'=>array('className'=>'User', 'foreignKey'=>'UserID'));
	public function loadComments($itemID, $offset, $limit){
		$this->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User', 'foreignKey'=>'UserID'))));
		$this->find('all', array('conditions'=>array('ItemComment.ItemID'=>$itemID), 'offset'=>$offset, 'limit'=>$limit));
	}
	
}