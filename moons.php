<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type,Authorization,X-User-Agent");
header("Access-Control-Allow-Credentials: true");
header("Access-Control: public");
header("access-control-allow-methods: GET,OPTIONS");
header("Content-Type: application/json");
error_reporting(0);
$url = "https://eveskunk.com/pos.php?sessid=bd039cc011b83383a53661584b1a3d55cab61ebf47a04d1ba8d6535f92c8f8b1&solarSystemID=30001577";

$html = file_get_contents($url);

//$dom = DOMDocument::loadHTML($html);
//print_r($dom);
//$data = $dom->getElementByID("");
$data = [];

preg_match_all("#<nobr>(.*?)</nobr>#", $html, $data);

$data = $data[1];

$finals = ["Fernite Carbide", "Crystalline Carbonide", "Tungsten Carbide", "Titanium Carbide"];
$gases = ["Evaporite Deposits", "Silicates", "Atmospheric Gases", "Hydrocarbons"];

$minFuel = PHP_INT_MAX;
$maxFuel = 0;
$averageFuel = 0;
$fuelNum = 0;

$minFinal = PHP_INT_MAX;
$maxFinal = 0;
$averageFinal = 0;
$finalNum = 0;

$minGas = PHP_INT_MAX;
$maxGas = 0;
$averageGas = 0;
$gasNum = 0;

foreach($data as $string){
	
	if(containsAny($string, $finals)){
		$match = [];
		preg_match("#<b>(.*?)</b>#", $string, $match);
		
		$match = $match[1];
		$match = substr($match, 0, strlen($match)-1);
		$amount = intval($match);
		
		if($amount < $minFinal){
			$minFinal = $amount;
		}
		
		if($amount > $maxFinal){
			$maxFinal = $amount;
		}
		
		$averageFinal += $amount;
		$finalNum++;
		
	} else if(containsAny($string, $gases)){
		$match = [];
		preg_match("#<b>(.*?)</b>#", $string, $match);
		
		$match = $match[1];
		$match = substr($match, 0, strlen($match)-1);
		$amount = intval($match);
		
		if($amount < $minGas){
			$minGas = $amount;
		}
		
		if($amount > $maxGas){
			$maxGas = $amount;
		}
		
		$averageGas += $amount;
		$gasNum++;
		
	} else if(stripos($string, "Fuel Blocks") !== false){
		$match = [];
		preg_match("#<b>(.*?)</b>#", $string, $match);
		
		$match = $match[1];
		$match = substr($match, 0, strlen($match)-1);
		$amount = intval($match);
		
		if($amount < $minFuel){
			$minFuel = $amount;
		}
		
		if($amount > $maxFuel){
			$maxFuel = $amount;
		}
		
		$averageFuel += $amount;
		$fuelNum++;
		
	}
	
}

$averageFinal = $averageFinal / $finalNum;
$averageGas = $averageGas / $gasNum;
$averageFuel = $averageFuel / $fuelNum;
echo json_encode(
		[
				"fuel"=>["min"=>$minFuel, "max"=>$maxFuel, "average"=>$averageFuel], 
				"final"=>["min"=>$minFinal, "max"=>$maxFinal, "average"=>$averageFuel], 
				"gas"=>["min"=>$minGas, "max"=>$maxGas, "average"=>$averageGas]]);

function containsAny($str, array $arr)
{
	foreach($arr as $a) {
		if (stripos($str,$a) !== false) return true;
	}
	return false;
}
?>