<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('Sanitize', 'Utility');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UploadsController extends AppController {

	public $name = 'Uploads';
	
	public $uses = array('Upload', 'UploadSession', 'User', 'Item', 'ItemImage', 'ItemRevision', 'File');
	private $bannedFileTypes = array('exe', 'zip', 'rar', 'jar', 'elf', 'dll', 'bat', 'sh');
	private $licenseTypes = array('cc','cc-sa','cc-nd','cc-nc','cc-nc-sa','cc-nc-nd','pd','gpl','lgpl','bsd','nokia','public');
	private $renderable = array('stl', 'obj', 'dxf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'pov');
	private $renderable3D = array('stl', 'obj');
	public function index(){
		if($this->Session->check('User.ID')){
			$this->set('pageTitle', 'Upload A Repable');
			$jsIncludes = array('jquery-ui-1.10.4.custom.min', 'jquery.tablednd', 'jquery.iframe-transport', 'jquery.fileupload', 'upload');
			$this->set('jsIncludes', $jsIncludes);
			$this->set('UploadHash', String::uuid());
		}else{
			$this->redirect('/login/upload');
		}
	}
	
	public function edit($itemID){
		if($this->Session->check('User.ID')){
			$this->set('pageTitle', 'Edit Repable');
			$jsIncludes = array('jquery-ui-1.10.4.custom.min', 'jquery.tablednd', 'jquery.iframe-transport', 'jquery.fileupload', 'upload');
			$this->set('jsIncludes', $jsIncludes);
			//$this->set('UploadHash', String::uuid());
			if(is_numeric($itemID) && $itemID >= 1){
			
				$itemID = (int)$itemID;
				
				$this->Item->bindImages();
				//$this->Item->bindUser();
				//$this->UploadSession->bindUser();
				
				$userID = $this->Session->read('User.ID');
				
				//$this->bindItemModel();
				//$item = $this->ItemRevision->find('first', array('conditions'=>array('RevisionID'=>$revisionID, 'Revision.Deleted'=>0, 'Item.Deleted'=>0, 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
				$this->Item->bindRevisions();
				$item = $this->Item->find('first', array('recursive'=>3, 'conditions'=>array('Item.ItemID'=>$itemID, 'Item.UserID'=>$userID)));
				if(isset($item['Item']['ItemID']) && $item['Item']['ItemID'] >= 1){
					
					
					$uploadSession = $this->UploadSession->find('first', array('recursive'=>0, 'conditions'=>array('UploadSession.UploadSessionID'=>$item['Item']['UploadSessionID'])));
					$this->UploadSession->id = $uploadSession['UploadSession']['UploadSessionID'];		
					$this->UploadSession->set('Processed', 0);
					$this->UploadSession->save();
					
					//$uploads = $this->Upload->find('all', array('conditions'=>array('Upload.UploadSessionID'=>$item['Item']['UploadSessionID'], 'Upload.Deleted'=>0)));
					
					$revisions = $this->ItemRevision->find('all', array('conditions'=>array('ItemRevision.ItemID'=>$itemID, 'ItemRevision.Deleted'=>false)));
					
					$revisionID = $item['Item']['RevisionID'];
					
					$revisionIndex = -1;
					for($i = 0; $revisionIndex==-1 && $i < count($revisions); $i++){
						if($revisions[$i]['ItemRevision']['RevisionID'] == $revisionID){
							$revisionIndex = $i;
						}
					}
					
					$files = $this->File->find('all', array('conditions'=>array('File.RevisionID'=>$revisionID)));
					
					
					//$comments = $this->ItemComment->find('all', array('conditions'=>array('ItemComment.ItemID'=>$itemID
					//$images = $this->ItemImage->find('all', array('conditions'=>array('ItemImage.ItemID'=>$itemID)));
					
					
					
					$this->set('UploadHash', $uploadSession['UploadSession']['UUID']);
					$this->set('itemID', $item['Item']['ItemID']);
					$this->set('pageTitle', 'Edit ' . $item['Item']['Name']);
					$this->set('itemData', $item['Item']);
					$this->set('files', $files);
					$this->set('revisions', $revisions);
					$this->set('revisionIndex', $revisionIndex);
					
					$this->render('/Uploads/index');
				}else{
					$this->render('/Errors/ItemNotFound');
				}
			}else{
				$this->render('/Errors/ItemNotFound');
			}
		}else{
			$this->redirect('/login/upload');
		}
	}
	private function getUploadSessionID($sessionHash){
		
		$visitorID = $this->Cookie->read('Visitor.ID');
		$userID = 0; 
		if($this->Session->check('User.ID')){
			$userID = $this->Session->read('User.ID');
		}
		$usID = $this->UploadSession->getSessionID($sessionHash, $visitorID, $userID);
		if($usID >= 1){
			return $usID;
		}
		/*$this->UploadSession->unbindModel(
			array('hasMany' => array('Upload'))
		);*/
		$uploads = $this->Cookie->read('Uploads');
		$this->UploadSession->save(array('UUID'=>$sessionHash, 'VisitorID'=>$visitorID, 'UserID'=>$userID));
		return $this->UploadSession->getInsertID();
	}
	/*public function save(){
		//$uploadType = $this->request->data('Upload.type');
		/*$uploadType = '';
		if(isset($_FILES['data']['tmp_name']['Upload']['PrimaryUpload'])){
			
		}else if(isset($_FILES['data']['tmp_name']['Upload']['SecondaryUpload')){
			
		}
		switch($uploadType){
			case 'primary':
				$this->primary();
				break;
			case 'secondary':
				$this->secondary();
				break;
		}
		
	}*/
	public function update(){		
		if(strlen($this->request->data('Upload.hash')) == 36){
			$hash = $this->request->data('Upload.hash');
			
			$visitorID = $this->Cookie->read('Visitor.ID');
			$userID = $this->Session->check('User.ID')?$this->Session->read('User.ID'):0;
			
			$itemName = Sanitize::html(trim($this->request->data('Upload.name')));
			$description = String::replaceURLs($this->request->data('Upload.description'), array('trim', array('Sanitize', 'html')));
			$instructions = String::replaceURLs($this->request->data('Upload.instructions'), array('trim', array('Sanitize', 'html')));
			$tags = Sanitize::paranoid(trim($this->request->data('Upload.tags')), array(',;'));
			$license = $this->request->data('License.type');
			$files = array_map('intval', explode(',', $this->request->data('Upload.files')));
			
		}
	}
	public function save(){
		$this->autoRender = false;
		$uploadHash = $this->request->data('Upload.hash');
		$uploadData = $this->handle_upload($uploadHash);
		$this->set('data', $uploadData);
		$this->render('/General/SerializeJson/', '');
	}
	public function complete(){	
		//$this->autoRender = false;
		$visitorID = $this->Cookie->read('Visitor.ID');
		$userID = 0;
		if($this->Session->check('User.ID')){
			$userID = $this->Session->read('User.ID');
		}
		if($userID >= 1){				
			if(strlen($this->request->data('Upload.hash')) == 36){
				$hash = $this->request->data('Upload.hash');
			
				$uploadSession = $this->UploadSession->find('first', array('recursive'=>0, 'conditions'=>array('UploadSession.UUID'=>$hash, 'UploadSession.UserID'=>$userID)));
				
				if(isset($uploadSession['UploadSession']['UploadSessionID']) && (int)$uploadSession['UploadSession']['UploadSessionID'] >= 1 &&  $uploadSession['UploadSession']['Processed']==0){
					$this->UploadSession->id = $uploadSession['UploadSession']['UploadSessionID'];
			
					$this->UploadSession->set('Processed', 1);
					$this->UploadSession->save();
			
					$uploadSessionID = $uploadSession['UploadSession']['UploadSessionID'];
					$itemID = 0;
					if($this->request->data('Upload.itemID') >= 1){
						$itemID = (int)$this->request->data('Upload.itemID');
						$item = $this->Item->find('first', array('conditions'=>array('Item.ItemID'=>$itemID, 'Item.UserID'=>$userID)));
						if(!isset($item['Item']['ItemID']) || $item['Item']['ItemID'] != $this->request->data('Upload.itemID')){
							
							//failure
							echo 'Not a valid revision';
							exit;
						}
					}
					
					$uploadExts = $this->Upload->find('list', array('fields'=>array('Upload.UploadID', 'Upload.FileExtension'), 'conditions'=>array('Upload.UploadSessionID'=>$uploadSessionID)));
					
					$uploadIds = $this->request->data('Upload.uploads');
					$fileData = $this->request->data('FilesData');
					
					$files = array();
					
					$itemName = Sanitize::html(trim($this->request->data('Upload.name')));
					$description = String::replaceURLs($this->request->data('Upload.description'), array('trim', array('Sanitize', 'html')));
					$instructions = String::replaceURLs($this->request->data('Upload.instructions'), array('trim', array('Sanitize', 'html')));
					$tags = Sanitize::paranoid(trim($this->request->data('Upload.tags')), array(',;'));
					$version = Sanitize::paranoid(trim($this->request->data('Upload.version')), array(',;._-'));
					$license = $this->request->data('Upload.license');
					
					
					if(!in_array($license, $this->licenseTypes)){
						$license == $this->licenseTypes[0];
					}
				
					$tags = preg_replace('/,+/', ',', preg_replace('/\s*,\s*/', ',', trim(preg_replace('/\s+/', ' ', str_replace(';', ',', $tags)))));

					
					$this->ItemRevision->save(array(
						'UploadSessionID'=>$uploadSessionID,
						'Name'=>$itemName,
						'Description'=>$description,
						'Instructions'=>$instructions,
						'Tags'=>$tags,
						'License'=>$license,
						'Version'=>$version,
						'DateCreated'=>CakeTime::sqlDatetime()
					));
					
					$revisionID = $this->ItemRevision->getInsertID();
					
					$search = $itemName . ' ' . $description . ' ' . str_replace(',', ' ', $tags);
					
					
					$itemData = array(
							'RevisionID'=>$revisionID,
							'Name'=>$itemName,
							'Description'=>$description,
							'Instructions'=>$instructions,
							'Tags'=>$tags,
							'Search'=>$search,
							'License'=>$license,
							'Published'=>0,
							'DateUpdated'=>CakeTime::sqlDatetime()
						);
					if((bool)$this->request->data('Upload.inappropriate')){
						$itemData['Flagged'] = true;
					}
					if($itemID >= 1){
						$this->Item->read(null, $itemID);
						$this->Item->set($itemData);
						$this->Item->save();					
					}else{
						$itemData += array(
							'UploadSessionID'=>$uploadSessionID,
							'UserID'=>$userID,
							'VisitorID'=>$visitorID,
							'SiteID'=>1,
							'DateCreated'=>CakeTime::sqlDatetime()
						);
						$this->Item->save($itemData);
						$itemID = $this->Item->getInsertID();
					}
					
					
					
					
					
					$previewImageFound = false;
					$firstPreviewable = -1;
					$first3D = -1;
					for($i = 0; $i < count($uploadIds); $i++){
						if(isset($fileData[$uploadIds[$i]])){
							//print_r($fileData[$uploadIds[$i]]);
							if(array_key_exists( $uploadIds[$i], $uploadExts)){
								$previewImage = false;
								if(array_search($uploadExts[$uploadIds[$i]], $this->renderable) !== -1 && $uploadExts[$uploadIds[$i]] != 'dxf'){
									if($firstPreviewable == -1){
										$firstPreviewable = $i;
									}
									if(array_search($uploadExts[$uploadIds[$i]], $this->renderable3D)){
										if($first3D == -1){
											$first3D = $i;
										}
									}
									if((bool)$fileData[$uploadIds[$i]]['PreviewImage']){
										if(!$previewImageFound){
											$previewImageFound = true;
											$previewImage = true;
										}
									}
								}
								$files[] = array(
									'UploadID'=>(int)$uploadIds[$i],
									'UploadSessionID'=>(int)$uploadSession['UploadSession']['UploadSessionID'],
									'RevisionID'=>(int)$revisionID,
									'Name'=>trim(preg_replace('/\s+/', ' ', $fileData[$uploadIds[$i]]['Filename'])) . '.' . $uploadExts[$uploadIds[$i]],
									'Downloadable'=>(bool)$fileData[$uploadIds[$i]]['AllowDownload'],
									'SortOrder'=>(int)$i,
									'Render'=>(bool)$fileData[$uploadIds[$i]]['Render'],
									'Preview'=>$previewImage
								);
							}
						}
					}
					if(!$previewImageFound){
						if($first3D >= 0){
							$files[$first3D]['Preview'] = true;
						}else if($firstPreviewable >= 0){
							$files[$firstPreviewable]['Preview'] = true;
						}					
					}
					
					
					//echo 'files:';
					//print_r($files);
					$this->File->saveMany($files);
					
					$this->ItemRevision->id = $revisionID;
					$this->ItemRevision->saveField('ItemID', $itemID);
					
					
					$dude = shell_exec(ROOT. DS . "app" . DS . "Console" . DS . "cake render " . $revisionID . " &");
					$this->redirect('/process/' . $itemID);
				}else{
					$item = $this->Item->find('first', array('conditions'=>array('Item.UploadSessionID'=>$uploadSession['UploadSession']['UploadSessionID'])));
					$this->redirect('/process/' . $item['Item']['ItemID']);
				}
			}else{
				$this->render('/Errors/ItemNotFound');
			}
		}else{
			$this->redirect('/login/upload');
		}
	}
	public function oldcomplete(){
		if(strlen($this->request->data('Upload.hash')) == 36){
			$hash = $this->request->data('Upload.hash');
			
			$visitorID = $this->Cookie->read('Visitor.ID');
			$userID = 0;
			if($this->Session->check('User.ID')){
				$userID = $this->Session->read('User.ID');
			}
			$itemName = Sanitize::html(trim($this->request->data('Upload.name')));
			$description = String::replaceURLs($this->request->data('Upload.description'), array('trim', array('Sanitize', 'html')));
			$instructions = String::replaceURLs($this->request->data('Upload.instructions'), array('trim', array('Sanitize', 'html')));
			$tags = Sanitize::paranoid(trim($this->request->data('Upload.tags')), array(',;'));
			$license = $this->request->data('License.type');
			$files = array_map('intval', explode(',', $this->request->data('Upload.files')));
			
			if(!in_array($license, $this->licenseTypes)){
				$license == $this->licenseTypes[0];
			}
			$tags = explode(',', $tags);
			foreach($tags as &$tag){
				$tag = trim($tag);
			}
			
			$data = $this->UploadSession->find('first', array('conditions'=>array('UploadSession.UUID'=>$hash, 'UploadSession.UserID'=>$userID)));
			
			if(isset($data['UploadSession']['UploadSessionID']) && (int)$data['UploadSession']['UploadSessionID'] >= 1){
				$uploadSessionID = $data['UploadSession']['UploadSessionID'];
				if(count($files)>=1){
					$this->Upload->updateAll(array('Upload.Enabled'=>0), array('Upload.UploaderID'=>$userID, 'Upload.UploadSessionID'=>$uploadSessionID));
					$this->Upload->updateAll(array('Upload.Enabled'=>1), array('Upload.UploaderID'=>$userID, 'Upload.UploadSessionID'=>$uploadSessionID, 'Upload.Deleted'=>0, 'OR'=>array('Upload.UploadID'=>$files)));
				}else{
					$this->Upload->updateAll(array('Upload.Enabled'=>1), array('Upload.UploaderID'=>$userID, 'Upload.UploadSessionID'=>$uploadSessionID, 'Upload.Deleted'=>0, 'Upload.DateUploaded<=' . CakeTime::sqlDatetime()));
					
				}
				
				$items = $this->Item->find('all', array('conditions'=>array('UploadSessionID'=>$uploadSessionID)));
				$search = $itemName . ' ' . $description . ' ' . implode(' ', $tags);
					
				$tags = implode(',', $tags);
				$itemData = array(
						'Name'=>$itemName,
						'Description'=>$description,
						'Instructions'=>$instructions,
						'Tags'=>$tags,
						'Search'=>$search,
						'License'=>$license,
						'Published'=>0
					);
				$this->ItemRevision->save(array(
						'Name'=>$itemName,
						'Description'=>$description,
						'Instructions'=>$instructions,
						'Tags'=>$tags,
						'License'=>$license
					));
				if(count($items) == 0){
					$itemData['UploadSessionID'] = $uploadSessionID;
					$itemData['UserID'] = $userID;
					$itemData['VisitorID'] = $visitorID;
					$itemData['DateCreated'] = CakeTime::sqlDatetime();
					
					$this->Item->save($itemData);
					$itemID = $this->Item->getInsertID();
					$dude = shell_exec(ROOT. DS . "app" . DS . "Console" . DS . "cake render " . $itemID . " &");
					//$this->createGalleryImages($itemID, $uploadSessionID);
					$this->redirect('/process/' . $itemID);
				}else{
				
					if((int)$this->request->data('Upload.itemID') >= 1){
						$itemID = (int)$this->request->data('Upload.itemID');
						$this->Item->read(null, $itemID);
						$this->Item->set($itemData);
						$this->Item->save();
						$dude = shell_exec(ROOT. DS . "app" . DS . "Console" . DS . "cake render rerender " . $itemID . " &");
						$this->redirect('/process/' . $itemID);
					}else{
						$this->redirect('/upload/');
					}
				}
			}else{
				$this->redirect('/upload/');
			}	
		}else{
			$this->redirect('/upload/');
		}
	}
	public function process($id){
		$this->set('pageTitle', 'Processing...');
		$jsIncludes = array('jquery.iframe-transport', 'jquery.fileupload', 'process-check');
		$this->set('jsIncludes', $jsIncludes);
		$this->set('processID', $id);
	}
	public function status($id){
		$this->autoRender = false;

		$data = $this->Item->find('first', array('conditions'=>array('Item.ItemID'=>$id), 'fields'=>array('Item.Processed')));
		if($data['Item']['Processed'] == 1){
			echo 'complete';
		}else{
			echo 'processing';
		}
		/*$data = $this->Item->find('first', array('conditions'=>array('Item.ItemID'=>$id)));
		$uploadSessionID = $data['Item']['UploadSessionID'];
		
		
		$uploads = $this->Upload->find('all', array('conditions'=>array('Upload.Deleted'=>0, 'Upload.UploadSessionID'=>$uploadSessionID)));
		$images = $this->ItemImage->find('count', array('conditions'=>array('ItemImage.ItemID'=>$id)));
		$renderCount = 0;
		for($i = 0; $i < count($uploads); $i++){
			switch(strtolower($uploads[$i]['Upload']['FileExtension'])){
				case "jpg":
				case "jpeg":
				case "png":
				case "gif":
				case "stl":
				case "obj":
					$renderCount++;
			}
		}
		if($renderCount == count($images)){
			echo 'complete';
		}else{
			echo 'processing';
		}*/
	}
	private function createGalleryImages($itemID, $sessionID){
		$this->Image = $this->Components->load('Image');
		$data = $this->Upload->find('all', array('conditions'=>array('Upload.Deleted'=>0, 'Upload.UploadSessionID'=>$sessionID)));
		$lowestID = 0;
		$lowestIDIndex = -1;
		//print_r($data);
		$original = '';
		$rendered = 0;
		for($i = 0; $i < count($data); $i++){			
			$saveLoc = IMAGE_GALLERY_DIR . DS . md5($sessionID) . '_' . md5(String::uuid()) . '.png';
			switch(strtolower($data[$i]['Upload']['FileExtension'])){
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'png':					
					$fileData = $this->Image->createGalleryImage($data[$i]['Upload']['FileLocation'], $saveLoc);
					$width = $fileData['width'];
					$height = $fileData['height'];
					$original = $data[$i]['Upload']['FileLocation'];
					break;
				case 'obj':
				case 'stl':
					$success = $this->Image->render3dObject($data[$i]['Upload']['FileLocation'], strtolower($data[$i]['Upload']['FileExtension']), $saveLoc);
					$width = $this->Image->galleryWidth;
					$height = $this->Image->galleryHeight;
					$rendered = 1;
					break;
			}
			if($success){
				$this->ItemImage->save(array('ItemID'=>$itemID, 'File'=>$saveLoc, 'Original'=>$original, 'Width'=>$width, 'Height'=>$height, 'Rendered'=>$rendered, 'DateCreated'=>CakeTime::sqlDatetime()));
			}
		}
		
	}
	/*public function primary(){
		$this->autoRender = false;
		$uploadHash = $this->request->data('Upload.hash');
		$uploadData = $this->handle_upload($uploadHash);
		$this->set('data', $uploadData);
		$this->render('/General/SerializeJson/', '');
	}
	public function secondary(){
		$this->autoRender = false;
		$uploadHash = $this->request->data('Upload.hash');
		$uploadData = $this->handle_upload($uploadHash);
		$this->set('data', $uploadData);
		$this->render('/General/SerializeJson/', '');
	}*/
	private function handle_upload($uploadSess){
		if(count($_FILES)){
		
			$fileInfo = pathinfo($_FILES['data']['name']['Upload']['PrimaryFile']);
			
			$file = round(microtime(true) * 10000) . '_' . md5(String::uuid()). '.' . $fileInfo['extension'];			
			
			
			$fileLoc = UPLOAD_DIR . DS . $file ;
			
			$userID = 0;
			if($this->Session->check('User.ID')){
				$userID = $this->Session->read('User.ID');
			}
			$visitorID = $this->Cookie->read('Visitor.ID');
			
			if(in_array($fileInfo['extension'], $this->bannedFileTypes)){
				return 'fileError';
			}
			
			
			move_uploaded_file($_FILES['data']['tmp_name']['Upload']['PrimaryFile'], $fileLoc);
			$mimeType = mime_content_type($fileLoc);
			$size = filesize($fileLoc);
			$hash = hash_file('md5', $fileLoc);
			$data = array(
				'UploaderID'=>$userID,
				'VisitorID'=>$this->Cookie->read('Visitor.ID'),
				'UploadSessionID'=>$this->getUploadSessionID($uploadSess),
				'FileLocation'=>$file,
				'Filename'=>$_FILES['data']['name']['Upload']['PrimaryFile'],
				'FileExtension'=>strtolower($fileInfo['extension']),
				'Filesize'=>$size,
				'MimeType'=>$mimeType,
				'Hash'=>$hash,
				'DateUploaded'=>CakeTime::sqlDatetime(),
				'RemoteAddress'=>$_SERVER['REMOTE_ADDR']
			);
			$success = $this->Upload->save($data);
			if($success){
				$uploadID = $this->Upload->getInsertID();
				//$this->
				/*if($this->Cookie->check('Uploads.' . $uploadSess)){
					$uploads = $this->Cookie->read('Uploads.'. $uploadSess);
				}else{
					$uploads = array('primary'=>array(), 'secondary'=>array(), 'date'=>CakeTime::sqlDatetime() );
				}
				$uploads[$type][] = $uploadID;
				$this->Cookie->write('Uploads.' . $uploadSess, $uploads);*/
				return array('uploadID'=>$uploadID, 'filename'=>strtolower($fileInfo['filename']), 'extension'=>strtolower($fileInfo['extension']));
			}else{
				return 'error';
			}
		}
	}
	public function secondaryview(){
		$this->render('/Uploads/SecondaryFiles', '');
	}
		
	public function delete(){
		$this->autoRender = false;
		$userQuery = array('Upload.VisitorID'=>$this->Cookie->read('Visitor.ID'));
		if($this->Session->check('User.ID')){
			$userQuery['Upload.UploaderID'] = $this->Session->read('User.ID');
		}
		$result = $this->Upload->updateAll(array('Upload.Deleted'=>'1'), array('Upload.UploadID'=>(int)$this->request->data('uploadID'),  'OR'=>$userQuery));
		if($this->Upload->getAffectedRows() == 1){			
			$this->set('data', array('status'=>'success', 'uploadID'=>(int)$this->request->data('uploadID')));
			$this->render('/General/SerializeJson/', '');
		}else{
			$this->set('data', array('status'=>'error', 'uploadID'=>(int)$this->request->data('uploadID')));
			$this->render('/General/SerializeJson/', '');
		}
	}
}
