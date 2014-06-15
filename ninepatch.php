<?php


function ninepatchResize($file, $width,$height,$back='trans'){
	
	if(!file_exists($file)){
		die("no image found");
	}
	$img = LoadPNG($file);
	$x = new bitgroups();
	$y = new bitgroups();
	if($width < imagesx($img) || $height < imagesy($img)){
		$now = getResize($width, $height,imagesx($img), imagesy($img), true);
		$later = array($width, $height);
		$width = $now[0];
		$height = $now[1];
	}
	for($i =0; $i < imagesx($img);$i++){
		$x->tick($i,imagecolorat($img,$i,0));
	}
	for($i =0; $i < imagesy($img);$i++){
		$y->tick($i,imagecolorat($img,0,$i));
	}
	$out = imagecreatetruecolor($width, $height);
	if($back != 'trans'){
		$color = imagecolorallocate($out, $back[0],  $back[1],  $back[2]);
		imagefill($out, 0, 0, $color);
	}else{
	imagealphablending( $out, false );
	imagesavealpha( $out, true );
		
	}
	$xlist = $x->calculateStrech($width);
	$ylist = $y->calculateStrech($height);
	for ($x=1; $x < $width; $x++) { 
		imagesetpixel($out, $x,0, imagecolorat($img, $xlist[$x], $ylist[1]));
	}
	for ($y=1; $y < $height-1; $y++) {
		imagesetpixel($out, 0,$y, imagecolorat($img,$xlist[1], $ylist[$y]));	
	}
	for ($x=1; $x < $width; $x++) { 
		for ($y=1; $y < $height; $y++) { 
			if($x < $width || $y < $height){
				if(isset($xlist[$x]) && isset($ylist[$y])){
					imagesetpixel($out, $x,$y, imagecolorat($img, $xlist[$x], $ylist[$y]));
				}else{
					echo "unkown $x:$y <br/>\n";
					
				}
			}else{
				echo "wrongpix $x:$y <br/>\n";
			}
		}
		# code...
	}

	if(isset($later) ){
		$resize = imagecreatetruecolor($later[0], $later[1]);
		imagealphablending( $resize, false );
	imagesavealpha( $resize, true );
		// Resize
		imagecopyresampled($resize, $out, 0, 0, 0, 0, $later[0], $later[1], $width, $height);
		return $resize;
	}else{
		return $out;		
	}



}
class bitgroups{
	var $last=-1;
	var $pos=-1;
	var $data=array();
	function tick($place,$color){
		$isblack = $color === 0 ? true : false;
		if($isblack != $this->last){
			$this->last = $isblack;
			$this->pos++;
			$this->data[$this->pos] = array($color, 1);		
		}else{
			$this->data[$this->pos][1]++;
		}
	}
	function calculateStrech($x=null){	
		if(count($this->data) == 1){
			$out = array();
			for ($xs=0; $xs < $x; $xs++) { 
				$out[] = floor(($this->data[0][1] /$x)*$xs) ;
			}
			return $out;
		}
		$total =  $this->colorWidth(-1);
		$black =  $this->colorWidth(0);
		$white =  $total - $black;
		$x = is_null($x)? $total : $x;
		$overbr = $x - $white;
		$outorder = array();
		$cur = 0;
		foreach ($this->data as $key => $value) {
			if($value[0] !=	0){ // just paste
				for ($i=0; $i < $value[1]; $i++) { 
					$outorder[] = $cur++;
				}
			}else{ // black
				$strech = round(($value[1]/ $black)*$overbr);
				for ($i=0; $i < $strech; $i++) { 
					$outorder[] = $cur +floor( ($i /$strech)*$value[1]  );
				}
				$cur += $value[1];
			}
		}
		if(count($outorder) != $x){
			$togo = ($x - count($outorder));
			for ($i=0; $i < $togo; $i++) { 
				$outorder[] = $cur-1;
			}
		}
		return $outorder;

	}
	function colorWidth($color=0){
		$c = 0;
		foreach ($this->data as $key => $value) {
			if($value[0] == $color || $color == -1){
				$c += $value[1];
			}
		}
		return $c;
	}

}
function getResize($sourceWidth, $sourceHeight, $targetWidth, $targetHeight,$inner){
	


	$sourceRatio = $sourceWidth / $sourceHeight;
	$targetRatio = $targetWidth / $targetHeight;

	if ( ($sourceRatio < $targetRatio && $inner == true) || ($sourceRatio > $targetRatio && $inner == false) ) {
	    $scale = $sourceWidth / $targetWidth;
	} else {
	    $scale = $sourceHeight / $targetHeight;
	}

	$resizeWidth = (int)($sourceWidth / $scale);
	$resizeHeight = (int)($sourceHeight / $scale);

	$cropLeft = (int)(($resizeWidth - $targetWidth) / 2);
	$cropTop = (int)(($resizeHeight - $targetHeight) / 2);

	return array($resizeWidth, $resizeHeight, $cropLeft, $cropTop);
}
function LoadPNG($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefrompng($imgname);

    /* See if it failed */
    if(!$im)
    {

imageAlphaBlending($im, true);
imageSaveAlpha($im, true);
        /* Create a blank image */
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        /* Output an error message */
        imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

