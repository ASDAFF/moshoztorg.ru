<?
class imlBarcode{
	static $kod;
	static $isp;
	static $number;
	static $width;
	static $height; 

	function imlBarcode($number){
		$this->setDefs();
		$this->number = $number;
		$this->getNum();
	}

	function setDefs(){
		$this->kod = array(
			"0" => array("a"=>"0001101","b"=>"0100111","c"=>"1110010"),
			"1" => array("a"=>"0011001","b"=>"0110011","c"=>"1100110"),
			"2" => array("a"=>"0010011","b"=>"0011011","c"=>"1101100"),
			"3" => array("a"=>"0111101","b"=>"0100001","c"=>"1000010"),
			"4" => array("a"=>"0100011","b"=>"0011101","c"=>"1011100"),
			"5" => array("a"=>"0110001","b"=>"0111001","c"=>"1001110"),
			"6" => array("a"=>"0101111","b"=>"0000101","c"=>"1010000"),
			"7" => array("a"=>"0111011","b"=>"0010001","c"=>"1000100"),
			"8" => array("a"=>"0110111","b"=>"0001001","c"=>"1001000"),
			"9" => array("a"=>"0001011","b"=>"0010111","c"=>"1110100")
		);
		$this->isp = array(
			"0" => array("2"=>"a","3"=>"a","4"=>"a","5"=>"a","6"=>"a","7"=>"a"),
			"1" => array("2"=>"a","3"=>"a","4"=>"b","5"=>"a","6"=>"b","7"=>"b"),
			"2" => array("2"=>"a","3"=>"a","4"=>"b","5"=>"b","6"=>"a","7"=>"b"),
			"3" => array("2"=>"a","3"=>"a","4"=>"b","5"=>"b","6"=>"b","7"=>"a"),
			"4" => array("2"=>"a","3"=>"b","4"=>"a","5"=>"a","6"=>"b","7"=>"b"),
			"5" => array("2"=>"a","3"=>"b","4"=>"b","5"=>"a","6"=>"a","7"=>"b"),
			"6" => array("2"=>"a","3"=>"b","4"=>"b","5"=>"b","6"=>"a","7"=>"a"),
			"7" => array("2"=>"a","3"=>"b","4"=>"a","5"=>"b","6"=>"a","7"=>"b"),
			"8" => array("2"=>"a","3"=>"b","4"=>"a","5"=>"b","6"=>"b","7"=>"a"),
			"9" => array("2"=>"a","3"=>"b","4"=>"b","5"=>"a","6"=>"b","7"=>"a")
		);
		$this->width = 102;
		$this->height = 40;
	}

	function getNum(){
		$first=substr($this->number,0,1);

		$im=imagecreate($this->width,$this->height);
		$p=imagecolorallocate($im,255,255,255);
		$s=imagecolorallocate($im,0,0,0);
		imagefill($im,0,0,$p);
		$isp_="";
		for ($j=2;$j<8;$j++) $isp_.=$this->isp[$first][$j];

		imagefilledrectangle($im,6,0,6,$this->height-5,$s);
		imagefilledrectangle($im,8,0,8,$this->height-5,$s);
		for($i=1;$i<strlen($this->number)-6;$i++){
			$curr=substr($this->number,$i,1);
			$is=substr($isp_,$i-1,1);
			$curr_code=$this->kod["$curr"]["$is"];
			$nach=9+7*($i-1);
			for($j=1;$j<8;$j++)
				if(substr($curr_code,$j-1,1)=="1")
					imagefilledrectangle($im,$nach+($j-1),0,$nach+($j-1),$this->height-10,$s);
			imagestring($im,2,$nach+1,$this->height-11,$curr,$s);
		};
		imagefilledrectangle($im,52,0,52,$this->height-5,$s);
		imagefilledrectangle($im,54,0,54,$this->height-5,$s);
		for($i=7;$i<strlen($this->number);$i++){
			$curr=substr($this->number,$i,1);
			$curr_code=$this->kod["$curr"]["c"];
			$nach=14+7*($i-1);
			for($j=1;$j<8;$j++)
				if(substr($curr_code,$j-1,1)=="1")
					imagefilledrectangle($im,$nach+($j-1),0,$nach+($j-1),$this->height-10,$s);
			imagestring($im,2,$nach+1,$this->height-11,$curr,$s);
		};
		imagefilledrectangle($im,98,0,98,$this->height-5,$s);
		imagefilledrectangle($im,100,0,100,$this->height-5,$s);
		imagestring($im,2,0,$this->height-11,$first,$s);

		// Выводим полученный код:
		header ('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
}
?>