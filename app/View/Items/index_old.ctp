<?php
if(isset($admin) && $admin == 1){
	echo '<!-- ';
	print_r($itemData);
	print_r($userData);
	echo '-->';
}
?>

<div align="center">
	<div id="ItemContent">
		<div class="ItemTitle"><?php echo $itemData['Item']['Name']; ?></div>
		<div class="ItemTopSection">
			<div class="GallerySection">
				<div class="GalleryImages">
					<?php
						for($i = 0; $i < count($itemData['ItemImage']); $i++){
						
							if($i == 0){
								echo '<div class="GalleryImage">';
							}else{
								echo '<div class="GalleryImage" style="visibility:hidden">';
								
							}
							$imgOffsetX = (620 - $itemData['ItemImage'][$i]['Width'])/2;
							$imgOffsetY = (360 - $itemData['ItemImage'][$i]['Height'])/2;
							if(isset($itemData['ItemImage'][$i]['View3d']) && $itemData['ItemImage'][$i]['View3d'] == true){
								echo '	<a href="#" onclick="view3d(' . $itemData['ItemImage'][$i]['ModelID'] . ')" style="display:none;">View 3D</a>';
							}
							echo '	<img src="'. $this->Html->url( DS . $itemData['ItemImage'][$i]['File']) . '" width="' . $itemData['ItemImage'][$i]['Width'] . 'px" height="' . $itemData['ItemImage'][$i]['Height'] . 'px" style="margin-left:'. $imgOffsetX .'px;margin-top:' . $imgOffsetY . 'px"/>';
							echo '</div>';
						}
					?>
				</div>
				<div id="GalleryThumbnails">
					<?php
						if(count($itemData['ItemImage'])>=5){
							echo '<div class="ThumbnailLeftArrow"></div>';
						}
					?>
					<div id="ThumbnailsStrip">	
						<div id="ThumbnailCont">					
							<?php
								for($i = 0; $i < count($itemData['ItemImage']); $i++){
									echo '<div class="ThumbnailImage"><img src="' . $this->Html->url( DS . $itemData['ItemImage'][$i]['Thumbnail']) . '"/></div>';
								}
							?>
						</div>
					</div>
					<?php
						if(count($itemData['ItemImage'])>=5){
							echo '<div class="ThumbnailRightArrow"></div>';
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
					<a class="btn btn-large" id="DownloadBtn" href="<?php echo $this->Html->url('/items/download/' . $itemData['Item']['ItemID'] . '/'); ?>">Download</a>
					<button class="btn btn-large btn-dropdown" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<?php
							for($i = 0; $i < count($filesData); $i++){
								echo '<li><a href="' . $this->Html->url('/items/download/' . $itemData['Item']['ItemID'] . '/' . $filesData[$i]['Upload']['UploadID'] . '/') . '">' . $filesData[$i]['Upload']['Filename'] . '</a></li>';
							}
						?>
						<li><a href="<?php echo $this->Html->url('/items/download/' . $itemData['Item']['ItemID'] . '/'); ?>">All Files</a></li>
					</ul>
				</div>
			</div>
			<div class="clearBoth">
				
			</div>			
		</div>
		<div class="DescriptionSection clearBoth">
			<div class="ItemInfoGroup">
				<div class="ItemInfoLabel">Description</div>
				<div class="ItemInfoContent"><?php echo $itemData['Item']['Description']; ?></div>
			</div>
			<div class="ItemInfoGroup">
				<div class="ItemInfoLabel">Instructions</div>
				<div class="ItemInfoContent"><?php echo $itemData['Item']['Instructions']; ?></div>
			</div>
			<div class="ItemInfoGroup">
				<div class="ItemInfoLabel">Tags</div>
				<div class="ItemInfoContent">
					<?php
						
						$tags = explode(',', $itemData['Item']['Tags']);
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
	</div>
</div>