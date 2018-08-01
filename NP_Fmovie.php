<?php

class NP_Fmovie extends NucleusPlugin
{

	function getName() { return 'Fmovie'; }
	function getAuthor() { return 'tokitake'; }
	function getURL() { return 'http://www.fukulog.com/'; }
	function getVersion() { return '0.2'; }
	
	function getDescription() {
		return 'FLV movie play';
	}
	function supportsFeature($what) {
		switch($what){
			case 'SqlTablePrefix':
				return 1;
			default:
				return 0;
		}
	}
	
	function getEventList() { return array('PreItem'); }
	
	function install() {
	}
	
	function doSkinVar($skintype) {
		echo '<script language="JavaScript" src="'.$this->getAdminURL().'mov.js"></script>';
	}

	function event_PreItem($data) { 
		$this->currentItem = &$data["item"]; 
		$this->currentItem->body = preg_replace_callback("/<\%media\((.*)\)%\>/Us", array(&$this, 'flvplayer'), $this->currentItem->body); 
		$this->currentItem->more = preg_replace_callback("/<\%media\((.*)\)%\>/Us", array(&$this, 'flvplayer'), $this->currentItem->more); 
	} 

	function flvplayer($matches){
		global $CONF; 
		$farray = array();
		$farray = explode("|",$matches[1]);

		$filename = $farray[0];
		$text = $farray[1];
		$width = isset($farray[2]) ? (int) $farray[2] : 0;
		$height = isset($farray[3]) ? (int) $farray[3] : 0;

		$searchdot = strrpos($filename,".");
		$tail = substr($filename,$searchdot,strlen($filename)-$searchdot+1);

		if($tail == ".flv"){
			if($width == 0){$width = 320;}
			if($height == 0){$height = 240;}
			$height = $height + 23;
			$blog =  htmlspecialchars($this->getAdminURL());
			if (!strstr($filename,'/')) {
				$media = $this->currentItem->authorid;
			}
			$media = '';
			$media = htmlspecialchars($CONF['MediaURL']. $media);
			$out = <<<EOD
<script language="JavaScript">writeFlash('$blog','$media','$filename','$width','$height')</script>
EOD;
		}else{
			$out = <<<EOD
<%media($filename|$text|$width|$height)%>
EOD;
		}

		return $out;

	}
}
?>