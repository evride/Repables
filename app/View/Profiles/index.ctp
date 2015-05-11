<div class="container-fluid">
	<h2 class="headerUnderline" style="margin:0 15px 10px 15px">Items by <?php echo $UserData['User']['Username']; ?></h2>
</div>
<div class="SearchResults">

	<?php
		if(count($items) == 0){
	?>
		<div>No results found<?php if(isset($tag) && strlen($tag)>=1){ echo 'for the tag &quot;' . $tag . '&quot;'; } ?></div>
	<?php
		}else{
			for($i = 0; $i < count($items); $i++){
				echo '<div class="SearchResult">';
				echo '<a href="' . $this->Html->url('/r/' . $items[$i]['Item']['ItemID'] . '/') . '">';
				echo '<img src="' . $this->Html->url('/' . $items[$i]['PreviewImage'][0]['File']) . '">';
				$itemName = strlen($items[$i]['Item']['Name']) >= 27?substr($items[$i]['Item']['Name'], 0, 24) . '...':$items[$i]['Item']['Name'];
				echo '<div class="ItemName" title="' . $items[$i]['Item']['Name'] . '">' . $itemName . '</div>';
				echo '</a>';
				echo '<div class="ItemUploader">';
				
				if(isset($items[$i]['User']['UserID']) && $items[$i]['User']['UserID'] >= 1){
					echo '<a href="' . $this->Html->url('/u/' . $items[$i]['User']['Username'] . '/') . '">' . $items[$i]['User']['Username'] . '</a>';
				}else{
					echo 'anonymous';
				}
				echo '</div>';
				echo '</div>';
				if($i % 4 == 3 || $i == count($items) - 1 ){
					echo '<div class="rowSpacer"></div>';
				}
			}
			if(isset($page) && isset($totalPages)){
				if($totalPages > 1){
				
					$minPage = min($totalPages - 10, $page - 5);
					$minPage = max($minPage, 1);
					
					$maxPage = $minPage + 10;
					$maxPage = min($maxPage, $totalPages);
					
					
					echo '<div class="PageSelect">';
					for($i = $minPage; $i <= $maxPage; $i++){
						if($i == $page){					
							echo '<a class="selectedPage" href="' . $this->Html->url('/explore/' . $i) . '">' . $i . '</a>';
						}else{
							echo '<a href="' . $this->Html->url('/explore/' . $i) . '">' . $i . '</a>';
						
						}
						
					}
					echo '</div>';
				}
			}
			if(isset($homePage) && $homePage == 1){
				echo $this->Html->link('View More', '/explore/2', array('class' => 'btn ViewMoreBtn'));
			}
		}
	?>
</div>