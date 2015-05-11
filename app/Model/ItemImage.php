<?php
class ItemImage extends AppModel{
	public $name = 'ItemImage';
	public $useTable = 'ItemImages';
	public $primaryKey = 'ImageID';
	/*public $hasMany = array(
		'Upload'=> array(	'className'	=>	'Upload',
							'foreignKey'=>	'UploadSessionID',
							'conditions'=>	array('Upload.Deleted'=>0)
						)
					);
	*/
}