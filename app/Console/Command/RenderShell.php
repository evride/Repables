<?php
/** *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Shell', 'Console');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeTime', 'Utility');
/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
 
class RenderShell extends AppShell {
	public $Image;
	public $FileConverter;
	public $controller;
	public $revisionID;
	public $uses = array('Item', 'ItemRevision', 'ItemImage', 'Upload', 'UploadSession', 'PreviewImage', 'File', 'ConvertedFile');
	public $initialized = false;
	public function initializexc(){
	
		$this->initialized = true;
		ini_set('display_errors',1);
		App::import('Component', 'Image');
		App::import('Component', 'FileConversion');
		
		$collection = new ComponentCollection();
		$this->controller = & new Controller();
		$this->Image = & new ImageComponent($collection);
		$this->FileConverter = & new FileConversionComponent($collection);
	}
	public function main(){
		$this->initializexc();
		$this->renderRev($this->args[0]);
	}
	private function renderRev($revisionID){
		
		$this->ItemRevision->bindModel(array('belongsTo'=>array('Item'=>array('foreignKey'=>'ItemID'))));
			
		$this->revisionID = (int)$revisionID;
		
		$revision = $this->ItemRevision->find('first', array('conditions'=>array('ItemRevision.RevisionID'=>$this->revisionID)));		
		$itemID = $revision['Item']['ItemID'];
		
	
		$this->renderImages($this->revisionID, $itemID);
		
		$this->Item->read(null, $itemID);
		$this->Item->id = $itemID;
		
		$this->Item->set('Published', 1);		
		$this->Item->set('DatePublished', CakeTime::sqlDatetime());						
		$this->Item->set('Processed', 1);
		$this->Item->set('Updated', 1);
		$this->Item->save();
	}
	
	public function rerender(){
		$this->initializexc();
		$items = $this->Item->find('all', array('conditions'=>array('Item.Updated'=>0, 'Item.Deleted'=>0, 'Item.ItemID<=94', 'Item.ItemID>=91')));
		for($i = 0; $i < count($items); $i++){
			//$this->ItemImage->deleteAll(array('ItemImage.ItemID'=>$this->itemID));
			//$this->PreviewImage->deleteAll(array('PreviewImage.ItemID'=>$this->itemID));
			$this->renderRev($items[$i]['Item']['RevisionID']);
		}
	}
	public function next(){
		$this->initializexc();
		$item = $this->Item->find('first', array('conditions'=>array('Item.Updated'=>0, 'Item.Deleted'=>0, 'Item.Published'=>1)));
		if(isset($item['Item']['RevisionID'])){
			//$this->ItemImage->deleteAll(array('ItemImage.ItemID'=>$this->itemID));
			//$this->PreviewImage->deleteAll(array('PreviewImage.ItemID'=>$this->itemID));
			$this->renderRev($item['Item']['RevisionID']);
		}
	}
	private function renderImages($revisionID, $itemID){
		
		
		//$uploads = $this->Upload->find('all', array('conditions'=>array('Upload.UploadSessionID'=>$uploadSessionID)));
		$this->Upload->bindModel(array('hasMany'=>array('ConvertedFile'=>array('foreignKey'=>'UploadID'), 'PreviewImage'=>array('foreignKey'=>'Source'), 'ItemImage'=>array('foreignKey'=>'Source', 'sort'=>"`ItemImage`.`ImageID` DESC"))));
		
		$data = $this->File->find('all', array('recursive'=>3, 'conditions'=>array('File.Render'=>1, 'File.RevisionID'=>$revisionID), 'order'=>array('File.SortOrder ASC')));
		
		
		
		$lowestID = 0;
		$lowestIDIndex = -1;
		
		$rendered = 0;
		$previewRendered = false;
		$itemImages = array();
		$convertedFiles = array();
		$newPreview = false;
		for($i = 0; $i < count($data); $i++){
			$width = 0;
			$height = 0;
			if(isset($data[$i]['Upload']['ItemImage']) && count($data[$i]['Upload']['ItemImage']) >= 1){
				
			}else{
				$imageIDName = md5($revisionID) . '_' . md5(String::uuid());
				$pngLoc = IMAGE_GALLERY_DIR . DS . $imageIDName . '.png';
				$jpegLoc = IMAGE_GALLERY_DIR . DS . $imageIDName . '.jpg';
				$process = false;
				$stlFile = "";
				switch(strtolower($data[$i]['Upload']['FileExtension'])){
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'png':
						$origImage = $this->Image->loadImage(UPLOAD_DIR . DS . $data[$i]['Upload']['FileLocation'], strtolower($data[$i]['Upload']['FileExtension']));
						
						
						
						$image = $this->Image->createGalleryImage($origImage);
						
						imagejpeg($image, WWW_ROOT . $jpegLoc, $this->Image->galleryQuality);
						$width = imagesx($image);
						$height = imagesy($image);
						imagedestroy($origImage);
						//images/gallery/a5771bce93e200c36f7cd9dfd0e5deaa_876e0e4f25397b55250f1ead4f760949.png
						
						$process = true;
						break;
					case 'obj':
						$stlIndex = -1;
						if(isset($data[$i]['Upload']['ConvertedFile']) && count($data[$i]['Upload']['ConvertedFile']) >= 1){
							for($e = 0; $e < count($data[$i]['Upload']['ConvertedFile']); $e++){
								if($data[$i]['Upload']['ConvertedFile'][$e]['Filetype'] == 'stl'){
									$stlIndex = $e;
									break;
								}
							}
						}
						if($stlIndex == -1){
							$stlFile = $this->FileConverter->convertObjToStl(UPLOAD_DIR . DS . $data[$i]['Upload']['FileLocation']);					
							$convertedFiles[] = array('UploadID'=>$data[$i]['Upload']['UploadID'], 'Filetype'=>'stl', 'Filename'=>$stlFile, 'DateCreated'=>CakeTime::sqlDatetime());
						}else{
							$stlFile = $data[$i]['Upload']['ConvertedFile'][$stlIndex]['Filename'];
						}
					case 'stl':
						if($stlFile=== ""){
							$stlFile = UPLOAD_DIR . DS . $data[$i]['Upload']['FileLocation'];
						}
						$povIndex = -1;
						if(isset($data[$i]['Upload']['ConvertedFile']) && count($data[$i]['Upload']['ConvertedFile']) >= 1){
							for($e = 0; $e < count($data[$i]['Upload']['ConvertedFile']); $e++){
								if($data[$i]['Upload']['ConvertedFile'][$e]['Filetype'] == 'pov'){
									$povIndex = $e;
									break;
								}
							}
						}
						if($povIndex == -1){
							$povFile = $this->FileConverter->convertStlToPov($stlFile);					
							$convertedFiles[] = array('UploadID'=>$data[$i]['Upload']['UploadID'], 'Filetype'=>'pov', 'Filename'=>$povFile, 'DateCreated'=>CakeTime::sqlDatetime());
						}else{
							$povFile = $data[$i]['Upload']['ConvertedFile'][$povIndex]['Filename'];
						}
						
						$tempPng = $this->FileConverter->renderPov($povFile);
						
						$image = $this->Image->loadImage($tempPng);
						
						//unlink($tempPng);
						$this->Image->convertJPEG($image, WWW_ROOT . $jpegLoc, $this->Image->galleryQuality);
							
						$process = true;
						break;
					case "svg":					
						$img = new Imagick();
						$img->readImage(UPLOAD_DIR . DS . $data[$i]['Upload']['FileLocation']);
						$img->setImageFormat("jpeg");
						$img->resizeImage(620, 360, imagick::FILTER_LANCZOS, 1, true);
						$width = $img->getImageWidth();
						$height = $img->getImageHeight();
						$img->writeImage(WWW_ROOT . $jpegLoc);					
						$img->clear();
						$img->destroy();
						
						$process = true;
						$image = $this->Image->loadImage(WWW_ROOT . $jpegLoc);
						break;
					case 'dxf':					
						//$this->Image->renderDXF($data[$i]['Upload']['FileLocation'], $pngLoc);
						break;
				}
				if($process){
					$thumbSaveLoc = IMAGE_GALLERY_DIR . DS . $imageIDName . '_thumb.jpg';
					$thumb = $this->Image->createGalleryThumbnail($image);
					$thumbWidth = imagesx($thumb);
					$thumbHeight = imagesy($thumb);
					imagejpeg($thumb, WWW_ROOT . $thumbSaveLoc, $this->Image->thumbnailQuality);
					imagedestroy($thumb);
					
					$asdf = imagesx($image);
					$sdf = imagesy($image);
					$itemImages[] = array('ItemID'=>$itemID, 'File'=>$jpegLoc, 'Source'=>$data[$i]['Upload']['UploadID'], 'Width'=>$asdf, 'Height'=>$sdf, 'Thumbnail'=>$thumbSaveLoc, 'ThumbnailWidth'=>$thumbWidth, 'ThumbnailHeight'=>$thumbHeight, 'Rendered'=>$rendered, 'DateCreated'=>CakeTime::sqlDatetime());
					
					if($data[$i]['File']['Preview'] == 1){
						$newPreview = 1;
					}					
					imagedestroy($image);
				}
			}
		}
		if(count($convertedFiles)){
			$this->ConvertedFile->saveMany($convertedFiles);
		}
		if(count($itemImages)>=1){
			$this->ItemImage->saveMany($itemImages);
		}
		if($newPreview || true){
			$this->Upload->bindModel(array(
				'hasMany'=>array('ConvertedFile'=>array('foreignKey'=>'UploadID'), 'PreviewImage'=>array('foreignKey'=>'Source'), 'ItemImage'=>array('foreignKey'=>'Source', 'sort'=>"`ItemImage`.`ImageID` DESC"))
				
			));
			$this->File->bindModel(array('belongsTo'=>array('ItemRevision'=>array('foreignKey'=>'RevisionID'))));
			$this->ItemRevision->bindModel(array('belongsTo'=>array('Item'=>array('foreignKey'=>'ItemID'))));
			$previewData = $this->File->find('first', array('recursive'=>3, 'conditions'=>array('File.Preview'=>1, 'File.RevisionID'=>$revisionID)));
			if(!isset($previewData['Upload']['PreviewImage']['PreviewImageID'])){
				$image = $this->Image->loadImage(WWW_ROOT . $previewData['Upload']['ItemImage'][0]['File']);
								
				$previewSaveLoc = IMAGE_GALLERY_DIR . DS . $itemID . '_' . md5(String::uuid()) . '.jpg';
				$previewImage = $this->Image->createPreviewImage($image);
				$previewWidth = imagesx($previewImage);
				$previewHeight = imagesy($previewImage);
				imagejpeg($previewImage, WWW_ROOT . $previewSaveLoc, $this->Image->thumbnailQuality);
				imagedestroy($previewImage);
				$previewImageData = array('ItemID'=>$previewData['ItemRevision']['Item']['ItemID'], 'File'=>$previewSaveLoc, 'ItemImageID'=>$previewData['Upload']['ItemImage'][0]['ImageID'], 'Source'=>$previewData['Upload']['UploadID'], 'Width'=>$previewWidth, 'Height'=>$previewHeight, 'DateCreated'=>CakeTime::sqlDatetime());
				$this->PreviewImage->save($previewImageData);
			}
		}
	}
	
}
