<?php
class File extends AppModel{
	public $name = 'File';
	public $useTable = 'Files';
	public $primaryKey = 'FileID';
	public $belongsTo = array(
		'Upload'=> array(	'className'	=>	'Upload',
							'foreignKey'=>	'UploadID',
							/*'conditions'=>	array('Upload.Deleted'=>0)*/
						)
					);
	public function bindImage(){
		$this->bindModel(array('hasOne'=>array('ItemImage'=>array('foreignKey'=>'FileID'))));
	}
}