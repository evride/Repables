<?php

App::uses('Component', 'Controller');
class ImageComponent extends Component {
	public $previewWidth = 200;
	public $previewHeight = 150;
	public $profileThumbWidth = 120;
	public $profileThumbHeight = 120;
	public $galleryWidth = GALLERY_WIDTH;
	public $galleryHeight = GALLERY_HEIGHT;
	public $galleryThumbnailWidth = 110;
	public $galleryThumbnailHeight = 64;
	public $galleryQuality = 90;
	public $thumbnailQuality = 85;
	public function createProfileThumbnail($imageLoc, $saveLoc, $x, $y, $size, $thumbSize){
		$srcImage = $this->loadImage($imageLoc);
		$image = imagecreatetruecolor($thumbSize, $thumbSize);
		imagecopyresampled($image, $srcImage, 0, 0, (int)$x, (int)$y, $thumbSize, $thumbSize, (int)$size, (int)$size);
		$success = imagejpeg($image, $saveLoc, $this->thumbnailQuality);
		imagedestroy($image);
		imagedestroy($srcImage);
		return $success;
	}
	public function createGalleryImage($image){//, $saveLoc){
		$scaled = $this->scaleTo($image, $this->galleryWidth, $this->galleryHeight, 0xFFFFFF, 1);
		return $scaled;
		/*$success = imagepng($scaled, $saveLoc);
		$retData = false;
		if($success){
			$retData = array('width'=>imagesx($scaled), 'height'=>imagesy($scaled));
		}
		imagedestroy($image);
		imagedestroy($scaled);
		return $retData;*/
	}
	public function createGalleryThumbnail($image){
		$thumb = $this->scaleTo($image, $this->galleryThumbnailWidth, $this->galleryThumbnailHeight, 0xFFFFFF, 1);
		
		return $thumb;
	}
	public function createPreviewImage($image){
		$scaled = $this->scaleCrop($image, $this->previewWidth, $this->previewHeight);
		return $scaled;
		/*$success = imagepng($scaled, $saveLoc);
		imagedestroy($scaled);
		return $success;
		*/
	}
	public function scaleCrop($srcImage, $width, $height){
		
		$image = imagecreatetruecolor($width, $height);
		
		$srcWidth = imagesx($srcImage);
		$srcHeight = imagesy($srcImage);
		
		$dWidth = $srcWidth;
		$dHeight = $srcHeight;
		
		$srcX = 0;
		$srcY = 0;
		
		if($srcWidth / $srcHeight < $width / $height){
			//height is bigger
			$dHeight = $srcWidth / $width * $height;
			$srcY = round(($srcHeight - $dHeight)/2);
		}else{
			//width is bigger
			$dWidth = $srcHeight / $height * $width;
			$srcX = round(($srcWidth - $dWidth)/2);
			
		}
		
		imagecopyresampled($image, $srcImage, 0, 0, $srcX, $srcY, $width, $height, $dWidth, $dHeight);
		return $image;
	}
	public function scaleTo($srcImage, $width, $height, $background = false, $backgroundAlpha = 1){
		//scales image down/up to the dimensions $width, $height but doesnt't crop
		//$background can be false or a color in 0xFF9900 format
		//$backgroundAlpha is a float between 0 and 1
		$srcWidth = imagesx($srcImage);
		$srcHeight = imagesy($srcImage);
		
		$destX = 0;
		$destY = 0;
		
		$drawWidth = $width;
		$drawHeight = $height;
		
		if($srcWidth / $srcHeight < $width / $height){
			//height is bigger
			echo 'h';
			
			//$srcWidth = $height / $srcHeight * $srcWidth;
			//$srcHeight = $height;
			
			$drawWidth = $height / $srcHeight * $srcWidth;
			
			if($background === false){
				$destX = 0;
				$width = $drawWidth;
			}else {
				$destX = round(($width - $drawWidth)/2);
				echo $destX . " " . $width . " " . $drawWidth;
			}
		}else{
			//width is bigger
			echo 'w';
			//$srcHeight = $width / $srcWidth * $srcHeight;
			//$srcWidth = $width;
			
			$drawHeight = $width / $srcWidth * $srcHeight;
			
			if($background === false){
				$destY = 0;
				$height = $drawHeight;
			}else {
				$destY = round(($height - $drawHeight)/2);
			}
		}
		
		$image = imagecreatetruecolor($width, $height);
		if($background !== false){
			if($backgroundAlpha >= 0 && $backgroundAlpha < 1){
				imagealphablending($image, false);
				imagesavealpha($image, true);
				$bgColor = imagecolorallocatealpha ( $image , 0xFF & ($background >> 16), 0xFF & ($background >> 8), 0xFF & $background , round((1 - $backgroundAlpha) * 127) );
			}else{
				$bgColor = imagecolorallocate( $image , 0xFF & ($background >> 16), 0xFF & ($background >> 8), 0xFF & $background);
			}
			imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
		}
		imagecopyresampled($image, $srcImage, $destX, $destY, 0, 0, $drawWidth, $drawHeight, $srcWidth, $srcHeight);
		return $image;
	}
	public function convertJPEG($image, $filename, $quality){
		$w = imagesx($image);
		$h = imagesy($image);
		$jpeg = imagecreatetruecolor($w, $h);
		imagecopy($jpeg, $image, 0, 0, 0, 0, $w, $h);
		imagejpeg($jpeg, $filename, $quality);
	}
	public function loadImage($loc, $fileExt = ""){
		$fileExtStart = strrpos($loc, '.');
		if($fileExt == ""){
			$fileExt = substr($loc, $fileExtStart+1);
		}
		$image;
		switch($fileExt){
			case 'jpg':
			case 'jpeg':
				$image = imagecreatefromjpeg($loc);
				break;
			case 'png':
				$image = imagecreatefrompng($loc);
				break;
			case 'gif':
				$image = imagecreatefromgif($loc);
				break;
		}
		return $image;
	}
}