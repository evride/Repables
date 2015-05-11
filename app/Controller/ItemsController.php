<?php

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('String', 'Utility');


class ItemsController extends AppController {
	public $uses = array('Upload', 'UploadSession', 'User', 'ProfileImage', 'ProfileImageThumbnail', 'Item', 'ItemImage', 'ItemComment', 'ItemView', 'ItemRevision', 'ItemRating', 'ItemFlag', 'Download', 'File');
	public function index($itemID, $revisionID = null){
		if(is_numeric($itemID) && $itemID >= 1){
			
			$itemID = (int)$itemID;
			$jsIncludes = array('view_item');
			$this->set('jsIncludes', $jsIncludes);
			
			$this->User->bindProfileImage();
			$this->Item->bindImages();
			$this->Item->bindComments();
			//$this->Item->bindUser();
			//$this->UploadSession->bindUser();
			
			$this->User->bindProfileThumbnail(50);
			$item = $this->Item->find('first', array('conditions'=>array('Item.ItemID'=>$itemID, 'Item.Deleted'=>0, 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate')), 'recursive'=>3));
			if(isset($item['Item']['ItemID']) && $item['Item']['ItemID'] >= 1){
				
				$userID = 0;
				if($this->Session->read('User.ID') >= 1){
					$userID = $this->Session->read('User.ID');
				}
				$visitorID = 0;
				if($this->Cookie->read('Visitor.ID') >= 1){
					$visitorID = $this->Cookie->read('Visitor.ID');
				}
				$item['TotalViews'] = $this->ItemView->find('count', array('conditions'=>array('ItemView.ItemID'=>$itemID)));
				$this->ItemView->save(array('ItemID'=>$itemID, 'UserID'=>$userID, 'VisitorID'=>$visitorID, 'DateViewed'=>CakeTime::sqlDatetime()));
				
				$this->User->bindProfileThumbnail(120);
				$uploaderID = $item['Item']['UserID'];
				if($uploaderID >= 1){
					$user = $this->User->find('first', array('conditions'=>array('User.UserID'=>$uploaderID)));
				}else{
					$user = array();
				}
				
				$revisions = $this->ItemRevision->find('all', array('conditions'=>array('ItemRevision.ItemID'=>$itemID, 'ItemRevision.Deleted'=>0), 'order'=>array('DateCreated DESC')));
				$defaultRevID = $item['Item']['RevisionID'];
				$nonDefaultRev = is_numeric($revisionID) && $revisionID != $defaultRevID;
				if($revisionID == null){
					$revisionID = $defaultRevID;
				}
				
				
				$files = $this->File->find('all', array('conditions'=>array('File.RevisionID'=>$revisionID)));
				
				/*$uploads = $this->Upload->find('all', array('conditions'=>array('Upload.UploadSessionID'=>$item['Item']['UploadSessionID'], 'Upload.Deleted'=>0)));
				
				$uploadIDs = array();
				for($i = 0; $i < count($uploads); $i++){
					$uploadIDs[] = (int)$uploads[$i]['Upload']['UploadID'];
				}*/
				$uploadIDs = array();
				for($i = 0; $i < count($files); $i++){
					$uploadIDs[] = (int)$files[$i]['Upload']['UploadID'];
				}
				
				$downloadsInfo = $this->Download->getDownloadsInfo($itemID, $uploadIDs);
				
				$downloadIDFileType = array();
				for($i = 0; $i < count($files); $i++){
					$downloadIDFileType[$files[$i]['Upload']['UploadID']] = $files[$i]['Upload']['FileExtension'];
				}
				for($i = 0; $i < count($item['ItemImage']); $i++){
					if(isset($downloadIDFileType[(int)$item['ItemImage'][$i]['Source']])){
						if($downloadIDFileType[(int)$item['ItemImage'][$i]['Source']] == "stl"){
							$item['ItemImage'][$i]['View3d'] = true;
							$item['ItemImage'][$i]['ModelID'] = (int)$item['ItemImage'][$i]['Source'];
						}
					}
				}
				
				//SELECT SUM(CASE WHEN `ItemRatings`.`PointValue`='-1' THEN 1 END) AS `DownVotes`, SUM(CASE WHEN `ItemRatings`.`PointValue`='1' THEN 1 END) AS `UpVotes` FROM `ItemRatings` WHERE `ItemRatings`.`ItemID`='82'
				
				$SelectedRating = $this->ItemRating->find('first', array('conditions'=>array('ItemRating.ItemID'=>$itemID, 'ItemRating.UserID'=>$userID, 'ItemRating.Deleted'=>0), 'fields'=>array('PointValue')));
				if(isset($SelectedRating['ItemRating']['PointValue'])){
					$SelectedRating = $SelectedRating['ItemRating']['PointValue'];
				}
				$RatingCounts = $this->ItemRating->find('all', array('conditions'=>array('ItemRating.ItemID'=>$itemID, 'ItemRating.Deleted'=>0),
							'fields'=>array("SUM(CASE WHEN `ItemRating`.`PointValue`='-1' THEN 1 END) AS `Downvotes`", "SUM(CASE WHEN `ItemRating`.`PointValue`='1' THEN 1 END) AS `Upvotes`")));
				$ratingData = array('Upvotes'=>$RatingCounts[0][0]['Upvotes'], 'Downvotes'=>$RatingCounts[0][0]['Downvotes'], 'SelectedVote'=>$SelectedRating);
				
				$item['DownloadsInfo'] = $downloadsInfo[0][0];
				
				//$comments = $this->ItemComment->find('all', array('conditions'=>array('ItemComment.ItemID'=>$itemID
				//$images = $this->ItemImage->find('all', array('conditions'=>array('ItemImage.ItemID'=>$itemID)));
				$revisionIndex = -1;
				$defaultRevIndex = -1;
				for($i = 0; ($revisionIndex==-1 || $defaultRevIndex==-1) && $i < count($revisions); $i++){
					if($revisions[$i]['ItemRevision']['RevisionID'] == $revisionID){
						$revisionIndex = $i;
					}
					if($revisions[$i]['ItemRevision']['RevisionID'] == $defaultRevID){
						$defaultRevIndex = $i;
					}
				}
				if($revisionIndex == -1){
					$this->set('revisionNotFound', true);
					$revisionIndex = $defaultRevIndex;
				}
				$this->set('pageTitle', $item['Item']['Name']);
				$this->set('itemData', $item);
				$this->set('filesData', $files);
				$this->set('revisions', $revisions);
				$this->set('revisionIndex', $revisionIndex);
				$this->set('defaultRevIndex', $defaultRevIndex);
				$this->set('ratingData', $ratingData);
				$this->set('userData', $user);				
			}else{
				$this->render('/Errors/ItemNotFound');
			}
		}else{
			$this->render('/Errors/ItemNotFound');
		}
		
	}
	public function download($revisionID, $fileID=0){
		$this->autoRender = false;
		$itemID = (int)$revisionID;
		$fileID = (int)$fileID;
		$userID = 0;
		if($this->Session->check('User.ID')){
			$userID = (int)$this->Session->read('User.ID');		
		}
		$visitorID = (int)$this->Cookie->read('Visitor.ID');
		$this->ItemRevision->bindItemModel();
		$revision = $this->ItemRevision->find('first', array('conditions'=>array('ItemRevision.RevisionID'=>$revisionID, 'ItemRevision.Deleted'=>0, 'Item.Deleted'=>0, 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
		if(count($revision) >= 1){
			$itemID = $revision['Item']['ItemID'];
			$uploadSessionID = $revision['Item']['UploadSessionID'];
			$fileError = false;
			$downloadName = "";
			$uploadID = 0;
			if($fileID >= 1){
				$uploadInfo = $this->File->find('first', array('conditions'=>array('File.FileID'=>$fileID, 'File.Downloadable'=>1, 'Upload.Deleted'=>0)));
				if(count($uploadInfo) == 0){
					header("HTTP/1.0 404 Not Found");
					exit;
				}else{
					$filename = UPLOAD_DIR . '/' . $uploadInfo['Upload']['FileLocation'];
					$uploadID = $uploadInfo['Upload']['UploadID'];
					$downloadName = $uploadInfo['File']['Name'];
				}
			}else{
				//download all files
				$uploadInfo = $this->File->find('all', array('conditions'=>array('File.RevisionID'=>$revisionID)));
				if(count($uploadInfo) == 0){				
					header("HTTP/1.0 404 Not Found");
					exit;
				}
				$downloadName = 'Repable_' . $itemID . '-' . $revision['ItemRevision']['RevisionID'] . '.zip';
				$filename = ITEM_ARCHIVE_DIR . DS . $downloadName;
				if(!file_exists($filename)){
					$zip = new ZipArchive();
					$zip->open($filename, ZipArchive::CREATE);
					for($i = 0; $i < count($uploadInfo); $i++){
						if(file_exists(UPLOAD_DIR . '/' . $uploadInfo[$i]['Upload']['FileLocation'])){
							$zip->addFile(UPLOAD_DIR . '/' . $uploadInfo[$i]['Upload']['FileLocation'], $uploadInfo[$i]['File']['Name']);
						}				
					}
					$zip->close();
				}
			}
			
			$this->Download->save(array('ItemID'=>$itemID, 'FileID'=>$fileID, 'UploadID'=>$uploadID, 'UserID'=>$userID, 'VisitorID'=>$visitorID, 'RemoteAddress'=>$_SERVER['REMOTE_ADDR'], 'Date'=>CakeTime::sqlDatetime()));
			
			
			
			$mm_type="application/octet-stream";

			header("Cache-Control: public, must-revalidate");
			
			header("Content-Type: " . $mm_type);
			header("Content-Length: " .(string)(filesize($filename)) );
			header('Content-Disposition: attachment; filename="'.$downloadName.'"');
			header("Content-Transfer-Encoding: binary\n");
							  
			readfile($filename);
		}
		exit;
	}
		
	public function view3d($fileID){
		$this->autoRender = false;
		$fileID = (int)$fileID;
		
		
		$uploadInfo = $this->Upload->find('first', 
			array('conditions'=>array('Upload.UploadID'=>$fileID/*, 'Upload.Deleted'=>0*/))
		);
		
		if(isset($uploadInfo['Upload']['UploadID']) && $uploadInfo['Upload']['UploadID'] >= 1){			
			$itemData = $this->Item->find('first', array('conditions'=>array('Item.UploadSessionID'=>$uploadInfo['Upload']['UploadSessionID'], 'Item.Deleted'=>0, 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
			
			if(isset($itemData['Item']['ItemID']) && $itemData['Item']['ItemID'] >= 1){
				
				$filename = UPLOAD_DIR . '/' . $uploadInfo['Upload']['FileLocation'];
				
				$mm_type = $uploadInfo['Upload']['MimeType'];
			
				header("Content-Type: " . $mm_type);
				header("Content-Length: " .(string)(filesize($filename)) );
				header('Content-Disposition: attachment; filename="'. $uploadInfo['Upload']['Filename'] .'"');
				header("Content-Transfer-Encoding: binary\n");
								  
				readfile($filename);
			
			}else{		
				header("HTTP/1.0 404 Not Found");
			}
		}else{		
			header("HTTP/1.0 404 Not Found");
		}
		exit;
	}
	public function comment(){
		$this->autoRender = false;
		$retData = array();
		if($this->Session->check('User.ID')){
			$userID = $this->Session->read('User.ID');
			$commentText = String::replaceURLs($this->request->data('ItemComment.comment'), array(array('Sanitize', 'stripScripts'), 'htmlentities'));
			$itemID = $this->request->data('ItemComment.itemID');
			if(strlen($commentText) >= 1){
				$this->Item->find('first', array('conditions'=>array('Item.ItemID'=>$itemID, 'Item.Deleted'=>0, 'Item.Published'=>1, 'Item.Processed'=>1, 'Item.Flagged <=' . (int)$this->Session->read('User.ShowInappropriate'))));
				if($itemID >= 1){
					$replyTo = $this->request->data('ItemComment.replyTo');
					if(is_null($replyTo)){
						$replyTo = 0;
					}				
					$currDate = CakeTime::sqlDatetime();
					
					
					$this->ItemComment->save(array('ItemID'=>$itemID, 'UserID'=>$userID, 'Comment'=>$commentText, 'ReplyTo'=>$replyTo, 'DateCreated'=>$currDate, 'RemoteAddress'=>$_SERVER['REMOTE_ADDR']));
					
					$profileThumb = $this->ProfileImageThumbnail->find('first', array('conditions'=>array('ProfileImageThumbnail.UserID'=>$userID, 'ProfileImageThumbnail.Width'=>50, 'ProfileImageThumbnail.Height'=>50)));
					
					if(isset($profileThumb['ProfileImageThumbnail']['UserID']) && $profileThumb['ProfileImageThumbnail']['UserID'] >= 1){
						$profileImage = $profileThumb['ProfileImageThumbnail']['File'];
					}else{
						$profileImage = 'img/generic_user_50_50.png';
					}
					
					$commentID = $this->ItemComment->getInsertID();
					$retData['date'] = $currDate;
					$retData['commentID'] = $commentID;
					$retData['comment'] = $commentText;
					$retData['profileImage'] = Router::url(DS . $profileImage);
					$retData['profileURL'] = Router::url('/u/' . $userID);
					$retData['deleteCommentURL'] = Router::url('/Items/deletecomment/' . $commentID);
					$retData['replyTo'] = $replyTo;
					$retData['status'] = 'success';
					$retData['username'] = $this->Session->read('User.Username');
					//$this->set('data', $retData);
					//$this->render('/Items/ItemComment');
				}else{					
					$retData['status'] = 'error';
					$retData['errorMessage'] = 'No item id.';
				}
			}else{
				$retData['status'] = 'error';
				$retData['errorMessage'] = 'Comment not long enough.';
			}
		}else{
			$retData['status'] = 'error';
			$retData['errorMessage'] = 'USer not logged in.';
		}
		$this->set('data', $retData);
		$this->render('/General/SerializeJson', '');
	}
	public function deletecomment($commentID){
		$this->autoRender = false;
		$retData = array('commentID'=>$commentID);
		$retData['status'] = 'fail';
		if($this->Session->check('User.ID') && is_numeric($commentID) && $commentID >= 1){
			$userID = $this->Session->read('User.ID');
			$this->ItemComment->unbindModel(array('belongsTo'=>array('User')));
			
			$this->ItemComment->updateAll(array('ItemComment.Deleted'=>1, 'ItemComment.DateDeleted'=>"'" . CakeTime::sqlDatetime() . "'"), array('ItemComment.ItemCommentID'=>(int)$commentID, 'ItemComment.UserID'=>$userID, 'ItemComment.Deleted'=>0));
			$retData['status'] = $this->ItemComment->getAffectedRows() == 1?"success":"error";
		}
		$this->set('data', $retData);
		$this->render('/General/SerializeJson', '');
	}
	public function rate($itemID, $revisionID){
		$retData = array('status'=>'login_required');
		if($this->Session->check('User.ID')){
			$retData['status'] = 'invalid';
			
			$itemID = (int)$itemID;
			$revisionID = (int)$revisionID;
			$pointValue = (int)$this->request->data('PointValue');
			$userID = $this->Session->read('User.ID');
			
			$this->ItemRevision->bindItemModel();
			$data = $this->ItemRevision->find('first', array('conditions'=>array('ItemRevision.RevisionID'=>$revisionID, 'ItemRevision.ItemID'=>$itemID, 'Item.Deleted'=>0, 'ItemRevision.Deleted'=>0)));
			
			if($itemID >= 1 && $revisionID >= 1){
				if(isset($data['ItemRevision']['RevisionID']) && isset($data['Item']['ItemID']) && $data['ItemRevision']['RevisionID'] == $revisionID && $data['Item']['ItemID'] == $itemID){
					$this->ItemRating->updateAll(array('Deleted'=>1), array('ItemID'=>$itemID, 'UserID'=>$userID));
					$this->ItemRating->save(array('ItemID'=>$itemID, 'RevisionID'=>$revisionID, 'UserID'=>$userID, 'PointValue'=>$pointValue, 'DateEntered'=>CakeTime::sqlDatetime()));
					
					$retData += array('ItemID'=>$itemID, 'RevisionID'=>$revisionID, 'PointValue'=>$pointValue);
					$retData['status'] = 'success';
				}
			}
		}		
		$this->set('data', $retData);
		$this->render('/General/SerializeJson', '');
	}
	public function flag($itemID, $revisionID){
		$retData = array('status'=>'login_required');
		if($this->Session->check('User.ID')){
			$retData['status'] = 'invalid';
			$userID = $this->Session->read('User.ID');
			$itemID = (int)$itemID;
			$revisionID = (int)$revisionID;
			if($itemID >= 1 && $revisionID >= 1){
				$this->ItemRevision->bindItemModel();
				$data = $this->ItemRevision->find('first', array('conditions'=>array('ItemRevision.RevisionID'=>$revisionID, 'ItemRevision.ItemID'=>$itemID, 'Item.Deleted'=>0, 'Item.Flagged'=>0, 'ItemRevision.Deleted'=>0)));
				if(isset($data['ItemRevision']['RevisionID']) && isset($data['Item']['ItemID']) && $data['ItemRevision']['RevisionID'] == $revisionID && $data['Item']['ItemID'] == $itemID){
					$flag = $this->ItemFlag->find('first', array('conditions'=>array('ItemFlag.ItemID'=>$itemID, 'ItemFlag.UserID'=>$userID)));
					if(isset($flag['ItemFlag']['FlagID']) && $flag['ItemFlag']['FlagID'] >= 1){
						$retData['status'] = 'duplicate';
					}else{
						$this->ItemFlag->save(array('UserID'=>$userID, 'ItemID'=>$itemID, 'RevisionID'=>$revisionID, 'Date'=>CakeTime::sqlDatetime()));
						$retData += array('ItemID'=>$itemID, 'RevisionID'=>$revisionID);
						$retData['status'] = 'success';
						
						$flagCount = $this->ItemFlag->find('count', array('conditons'=>array('ItemFlag.ItemID'=>$itemID)));
						if($flagCount >= 3){
							$this->Item->read(null, $itemID);
							$this->Item->id = $itemID;
							$this->Item->set(array(
								'Flagged'=>true
								)
							);
							$this->Item->save();
						}
					}
				}
			}
		}
		$this->set('data', $retData);
		$this->render('/General/SerializeJson', '');
	}
}