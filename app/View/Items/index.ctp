<?php
if(isset($admin) && $admin == 1){
	echo '<!-- ';
	print_r($itemData);
	print_r($userData);
	echo '-->';
}
?>

<div align="center">
	<div id="ItemContent" data-item="<?php echo $itemData['Item']['ItemID'];?>" data-revision="<?php echo $revisions[$revisionIndex]['ItemRevision']['RevisionID'];?>">
		<div class="ItemTitle"><?php echo $itemData['Item']['Name']; ?></div>
		
		<div id="View3DContainer" class="ItemTopSection">
			<button class="close" href="#">&times;</button>
			<div class="progress">
			  <div class="bar" style="width: 10%;"></div>
			</div>
		</div>
		<div id="ItemDisplay" class="ItemTopSection">
			<div class="GallerySection">
				<div class="GalleryImages">
					<?php
						for($i = 0; $i < count($itemData['ItemImage']); $i++){
							
							if($i == 0){
								echo '<div class="GalleryImage">';
							}else{
								echo '<div class="GalleryImage" style="display:none">';
								
							}
							$imgOffsetX = (620 - $itemData['ItemImage'][$i]['Width'])/2;
							$imgOffsetY = (360 - $itemData['ItemImage'][$i]['Height'])/2;
							if(isset($itemData['ItemImage'][$i]['View3d']) && $itemData['ItemImage'][$i]['View3d'] == true){
								echo '	<a class="btn View3dBtn" href="#" onclick="view3d(' . $itemData['ItemImage'][$i]['ModelID'] . ')" >3D View</a>';
							}
							echo '	<img src="'. $this->Html->url( DS . $itemData['ItemImage'][$i]['File']) . '" width="' . $itemData['ItemImage'][$i]['Width'] . 'px" height="' . $itemData['ItemImage'][$i]['Height'] . 'px" style="margin-left:'. $imgOffsetX .'px;margin-top:' . $imgOffsetY . 'px"/>';
							echo '</div>';
						}
					?>
				</div>
				<div id="GalleryThumbnails">
					<?php
						if(count($itemData['ItemImage'])>=5){
							echo '<a href="#" class="ThumbnailLeftArrow"></a>';
						}
					?>
					<div class="ThumbnailsStrip">						
						<div class="ThumbnailCont" style="width: <?php echo count($itemData['ItemImage']) * 110; ?>px;">
							<?php
							for($i = 0; $i < count($itemData['ItemImage']); $i++){
								echo '<div class="ThumbnailImage"><img src="' . $this->Html->url( DS . $itemData['ItemImage'][$i]['Thumbnail']) . '"/></div>';
							}
							?>
						</div>
					</div>
					<?php
						if(count($itemData['ItemImage'])>=5){
							echo '<a href="#" class="ThumbnailRightArrow"></a>';
						}
					?>
				</div>
			</div>
			<div class="ItemTopRightPanel">
				<div class="UploaderSection">
					
					<?php
						$anonymousUser = true;
						if(isset($userData['User']['UserID'])){
							if($userData['User']['UserID'] >= 1){
								$anonymousUser = false;
							}
						}
						if($anonymousUser){
							echo $this->element('anonymous_user');
						}else{
					?>
					<div class="UploaderImage">
						<?php
							$uploaderImg = "img/generic_user_120_120.png";
							if(isset($userData['ProfileImageThumbnail']['File']) && strlen($userData['ProfileImageThumbnail']['File']) >= 1){
								$uploaderImg = $userData['ProfileImageThumbnail']['File'];
							}
							echo '<img src="' . $this->Html->url( DS . $uploaderImg) . '" />';
						?>
						<div class="Username">
							<?php
								echo $userData['User']['Username'];
							?>
						</div>
					</div>
					<div class="UploaderInfoGroup">
					<?php
						if(isset($userData['User']['Fullname']) && strlen($userData['User']['Fullname']) >= 1){
					?>
						<div class="FullName UploaderInfoItem">
							<span class="UploaderInfoLabel">Full Name</span>
							<span>
							<?php
								echo $userData['User']['Fullname'];
							?>
							</span>
						</div>
						<?php
							}
							
							if(isset($userData['User']['Location']) && strlen($userData['User']['Location']) >= 1){
						?>
						<div class="Location UploaderInfoItem">
							<span class="UploaderInfoLabel">Location</span>
							<span>
							<?php
								echo $userData['User']['Location'];
							?>
							</span>
						</div>
						<?php
							}
							
							if(isset($userData['User']['Company']) && strlen($userData['User']['Company']) >= 1){
						?>
						<div class="Company UploaderInfoItem">
							<span class="UploaderInfoLabel">Company</span>
							<span>
							<?php
								echo $userData['User']['Company'];
							?>
							</span>
						</div>
						<?php
							}
						?>
						<div class="ViewProfileBtn UploaderInfoItem">
							<span class="UploaderInfoLabel"><a href="<?php echo $this->Html->url('/u/' . $userData['User']['Username'] . '/'); ?>">View Profile &gt;</a></span>
						</div>
					</div>
					<?php
						}
					?>
				</div>
				<div class="btn-group" id="DownloadDropdown">
					<a class="btn btn-large" id="DownloadBtn" href="<?php echo $this->Html->url('/items/download/' . $revisions[$revisionIndex]['ItemRevision']['RevisionID']. '/'); ?>">Download</a>
					<button class="btn btn-large btn-dropdown">
						<span class="caret"></span>
					</button>
				</div>
			</div>
			<div class="downloadSection">
				<table id="DownloadsTable">
					<tr><th class="FilenameCell" width="400px">Filename</th><th align="right">Downloads</th><th align="right">Size</th><th align="right">Uploaded</th><th></th></tr>
					
					<?php
						for($i = 0; $i < count($filesData); $i++){
							
							$timeSinceUpload = CakeTime::timeAgoInWords($filesData[$i]['Upload']['DateUploaded'], array('accuracy' => array('hour'=>'hour', 'day'=>'day', 'month' => 'month', 'week'=>'week'), 'end' => '+1 year'));
							
							$fileSizeLog = log($filesData[$i]['Upload']['Filesize'], 1024);
							$fileSize = 0;
							if($fileSizeLog < 1){
								$fileSize = $filesData[$i]['Upload']['Filesize'] . " bytes";
							}else if($fileSizeLog < 2){								
								$fileSize = round($filesData[$i]['Upload']['Filesize'] / 1024) . " kB";
							}else if($fileSizeLog < 3){								
								$fileSize = round($filesData[$i]['Upload']['Filesize'] / 1048576, 1) . " MB";
							}else if($fileSizeLog > 3){								
								$fileSize = round($filesData[$i]['Upload']['Filesize'] / 1073741824, 2) . " GB";
							}
							
							echo '<tr>';
							echo '<td class="FilenameCell">' . $filesData[$i]['File']['Name'] . '</td>';
							echo '<td align="right">' . $itemData['DownloadsInfo'][$filesData[$i]['Upload']['UploadID']] . '</td><td  align="right" title="'. $filesData[$i]['Upload']['Filesize'] . ' bytes">' . $fileSize . '</td><td  align="right" title="' . $filesData[$i]['Upload']['DateUploaded'] . '">' . $timeSinceUpload . '</td>';
							echo '<td class="DownloadCell"><a class="btn" href="' . $this->Html->url('/items/download/' . $revisions[$revisionIndex]['ItemRevision']['RevisionID'] . '/' . $filesData[$i]['File']['FileID'] . '/') . '"><i class="icon-download-alt"></i> Download</a></td>';
							echo '</tr>';
						}
					?>
					
				</table>
			</div>	
			<div class="clearBoth"></div>		
		</div>
		<div class="DescriptionSection clearBoth row-fluid">
			<div class="span8">
				<?php
					if(isset($revisions[$revisionIndex]['ItemRevision']['Description']) && strlen($revisions[$revisionIndex]['ItemRevision']['Description']) >= 1){
				?>
				<div class="ItemInfoGroup">
					<div class="ItemInfoLabel">Description</div>
					<div class="ItemInfoContent"><?php echo $revisions[$revisionIndex]['ItemRevision']['Description']; ?></div>
				</div>
				<?php
					}
					
					if(isset($revisions[$revisionIndex]['ItemRevision']['Instructions']) && strlen($revisions[$revisionIndex]['ItemRevision']['Instructions']) >= 1){
				?>
				<div class="ItemInfoGroup">
					<div class="ItemInfoLabel">Instructions</div>
					<div class="ItemInfoContent"><?php echo $revisions[$revisionIndex]['ItemRevision']['Instructions']; ?></div>
				</div>
				<?php
					}
					if(isset($revisions[$revisionIndex]['ItemRevision']['License']) && strlen($revisions[$revisionIndex]['ItemRevision']['License']) >= 1){
						$licenses = array();
						$licenses['cc'] = 'Attribution - Creative Commons';
						$licenses['cc-sa'] = 'Attribution - Share Alike - Creative Commons';
						$licenses['cc-nd'] = 'Attribution - No Derivatives - Creative Commons';
						$licenses['cc-nc'] = 'Attribution - Non-Commercial - Creative Commons';
						$licenses['cc-nc-sa'] = 'Attribution - Non-Commercial - Share Alike';
						$licenses['cc-nc-nd'] = 'Attribution - Non-Commercial - No Derivatives';
						$licenses['pd'] = 'Creative Commons - Public Domain Dedication';
						$licenses['gpl'] = 'Creative Commons - GNU GPL';
						$licenses['lgpl'] = 'Creative Commons - LGPL';
						$licenses['bsd'] = 'BSD License';
						$licenses['nokia'] = 'Nokia';
						$licenses['public'] = 'Public Domain';
				?>
				<div class="ItemInfoGroup">
					<div class="ItemInfoLabel">License</div>
					<div class="ItemInfoContent"><?php echo $licenses[$revisions[$revisionIndex]['ItemRevision']['License']]; ?></div>
				</div>
				<?php
					}
					
					if(isset($revisions[$revisionIndex]['ItemRevision']['Tags']) && strlen($revisions[$revisionIndex]['ItemRevision']['Tags']) >= 1){
				?>
				<div class="ItemInfoGroup">
					<div class="ItemInfoLabel">Tags</div>
					<div class="ItemInfoContent">
						<?php
							
							$tags = explode(',', $revisions[$revisionIndex]['ItemRevision']['Tags']);
							$tagLinks = array();
							for($i = 0; $i < count($tags); $i++){
								if(strlen($tags[$i]) >= 1){
									$tagLinks[] =  '<a href="' . $this->Html->url('/tag/' . urlencode($tags[$i])) . '">'. $tags[$i] . '</a>';
								}
							}
							echo implode(', ', $tagLinks);
						
						?>			
					</div>
				</div>
				<?php
					}
				?>
				<div class="ItemInfoGroup">
					<div class="ItemInfoLabel">Comments</div>
					
					<div class="ItemInfoContent" id="ItemComments">
					<?php
						for($i = 0; $i < count($itemData['ItemComment']); $i++){
							$itemComment = $itemData['ItemComment'][$i];
							
							$thumbnailLink = "img/generic_user_50_50.png";
					
							if(isset($itemComment['User']['ProfileImageThumbnail']['File'])){
								if(strlen($itemComment['User']['ProfileImageThumbnail']['File']) >= 1){
									$thumbnailLink = $itemComment['User']['ProfileImageThumbnail']['File']; 
								}
							}
							
							echo '<div class="ItemComment" id="CommentNum' . $itemComment['ItemCommentID'] . '">';
							echo '<div class="CommentUserThumbnail"><img src="' . $this->Html->url(DS . $thumbnailLink, true) . '"></div>';
							echo '<div class="CommentTextCont">';
							
							$timeSinceComment = CakeTime::timeAgoInWords($itemComment['DateCreated'], array('accuracy' => array('month' => 'month', 'week'=>'week'), 'end' => '1 year'));
							echo '<div class="CommentUsername">';
							echo '<a href="' . $this->Html->url('/u/' . $itemComment['User']['Username']) . '">';
							if($itemComment['User']['UserID'] == $itemData['Item']['UserID'] && $itemData['Item']['UserID'] >= 1){
								echo  '<strong>' . $itemComment['User']['Username'] . '</strong>';
							}else{
								echo $itemComment['User']['Username'];
							}
							echo '</a> - <span class="CommentDate" title="' . $itemComment['DateCreated'] . '">' . $timeSinceComment . '</span>';
							if((int)$itemComment['User']['UserID'] == (int)$userID && (int)$userID >= 1){
								echo ' - <span class="DeleteComment"><a href="' . $this->Html->url('/items/deletecomment/' . $itemComment['ItemCommentID']) . '">Delete</a></span>';
							}
							echo '</div>';
							echo '<div class="CommentBody">' . $itemComment['Comment'] . '</div>';
							echo '</div>';
							echo '<div class="clearBoth"></div>';
							echo '</div>';
						}
						
						
						if(isset($userID) && (int)$userID >=1){
							
							echo $this->Form->create('ItemComment', array('url'=>array('controller'=>'items', 'action'=>'comment'), 'id'=>'ItemCommentForm'));
							
							echo $this->Form->input('itemID', array('type'=>'hidden', 'value'=>$itemData['Item']['ItemID']));
							echo $this->Form->input('replyTo', array('type'=>'hidden', 'value'=>0));
							echo $this->Form->textarea('comment', array('placeholder'=>'Add A Comment', 'id'=>'CommentText'));
							echo $this->Form->submit('Submit', array('class'=>'btn btn-primary btn-small'));
							echo $this->Form->end();
						}else{
							echo '<div><a href="' . $this->Html->url('/login') . '">Sign in</a> to add a comment.</div>';
						}
						
					?>
					</div>
				</div>
			</div>
			<ul class="ItemSidebar">
				<li class="ItemCount">
					<table>
						<tr><th>Views</th><th>Downloads</th></tr>
						<tr><td><?php echo $itemData['TotalViews'];?></td><td><?php echo $itemData['DownloadsInfo']['Total'];?></td></tr>
					</table>
				</li>
				<li class="Ratingbar">
				<?php
					if($ratingData['Upvotes'] + $ratingData['Downvotes'] >=1 ){
						echo '<div style="width:' . 100 * $ratingData['Upvotes'] / ($ratingData['Upvotes'] + $ratingData['Downvotes']) . '%" class="upvotes" title="' . $ratingData['Upvotes'] . ' upvotes"></div>';
						echo '<div style="width:' . 100 * $ratingData['Downvotes'] / ($ratingData['Upvotes'] + $ratingData['Downvotes']) . '%" class="downvotes" title="' . $ratingData['Downvotes'] . ' downvotes"></div>';
						
					}else{
						echo '<div class="novotes" alt="0 Votes"></div>';
						
					}
				?>
				</li>
				<li>
					<a href="#" class="rateBtn rate-up<?php if($ratingData['SelectedVote'] == 1){ echo ' rate-up-selected'; } ?>"><span></span></a><a href="#" class="rateBtn rate-down<?php if($ratingData['SelectedVote'] == -1){ echo ' rate-down-selected'; } ?>"><span></span></a>
					<div class="clearBoth"></div>
				</li>
				<?php
					if($userID == $userData['User']['UserID']){
				?>			
					<li class="ButtonRow">
						<a href="<?php echo $this->Html->url('/edit/' . $itemData['Item']['ItemID']); ?>">
							<i class="icon-pencil"></i>
							<span>Edit Item</span>
						</a>
					</li>
				<?php
					}else{
				?>
				
				<li class="FlagInappropriate ButtonRow">
					<a href="<?php echo $this->Html->url('/Items/flag/' . $itemData['Item']['ItemID']); ?>">
						<i class="icon-flag"></i>
						<span>Report as inappropriate</span>
					</a>
				</li>
				<?php
					}
				?>
				<li class="Revisions">
					<h4>Revisions</h4>
					<ul class="RevisionList">
						<?php
							for($i = 0; $i < count($revisions); $i++){
								echo '<li'. ($i == $revisionIndex?' class="selectedRevision"':'') . '><a href="' . $this->Html->url('/r/' . $itemData['Item']['ItemID'] . '/' .$revisions[$i]['ItemRevision']['RevisionID']) . '">';
								echo '<div title="Revision #' . $revisions[$i]['ItemRevision']['RevisionID'] . '">';
								if(isset($revisions[$i]['ItemRevision']['Version']) && strlen($revisions[$i]['ItemRevision']['Version']) >= 1){
									echo 'Version ' . $revisions[$i]['ItemRevision']['Version'];
								}else{
									echo 'Revision #' . $revisions[$i]['ItemRevision']['RevisionID'];
								}
								echo '</div><div class="RevDate">';
								echo CakeTime::timeAgoInWords($revisions[$i]['ItemRevision']['DateUpdated'], array('accuracy' => array('month' => 'month', 'week'=>'week'), 'end' => '1 year'));
								echo '</div>';
								echo '</a></li>';
								
							}
						?>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>