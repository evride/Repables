<?php
class Download extends AppModel{
	public $name = 'Download';
	public $useTable = 'Downloads';
	public $primaryKey = 'DownloadID';
	
	public function getDownloadsInfo($itemID, $files){
		$files = array_map('intval', $files);
		$itemID = (int)$itemID;
		$filesStr = '';
		if(count($files) >= 1){
			for($i = 0; $i < count($files); $i++){
				$filesStr .= ", SUM(`Download`.`UploadID`='$files[$i]' AND `Download`.`ItemID`='$itemID') AS `$files[$i]`";
			}
		}
		return $this->query("SELECT SUM(`Download`.`ItemID`='$itemID') AS `Total` $filesStr FROM `Downloads` AS `Download`");
	}
}