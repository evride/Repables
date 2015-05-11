<?php
class ConvertedFile extends AppModel{
	public $name = 'ConvertedFile';
	public $useTable = 'ConvertedFiles';
	public $primaryKey = 'CFID';
	
	public function bindUpload(){
		$this->bindModel(array('belongsTo'=>array(
			'Upload'=> array(	'className'	=>	'Upload',
							'foreignKey'=>	'UploadID',
							/*'conditions'=>	array('Upload.Deleted'=>0)*/
						)
					)));
	}
}