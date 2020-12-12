<?
require_once("dbconfig.php");
require_once("classes.php");
header('Content-Type: text/html; charset=utf-8');

DBConfig::connect();
DBConfig::$ActualArrayFilms = DBConfig::queryGet("SELECT * FROM `toplist` order by num");
$parser = new Parser();
if(!$parser->isLastParsToday()){
	$newList = array();
	$newList = $parser->executePars();
	$modification = $parser->sortOldList(DBConfig::$ActualArrayFilms, $newList);
	if(count($modification["toDelte"]) > 0){
		foreach ($modification["toDelte"] as $key => $value) {
			DBConfig::querySet("DELETE FROM `toplist` WHERE idkp = ".$key);
		}
	}
	if(count($modification["toUpdate"]) > 0){
		foreach ($modification["toUpdate"] as $value) {
			DBConfig::querySet("UPDATE `toplist` set num = ".$value["num"].', date = "'.$value["date"].'", rating = "'.$value["rating"].'" WHERE idkp = '.$value["idkp"]);
		}
	}
	if(count($modification["toInsert"]) > 0){
		foreach ($modification["toInsert"] as $value) {
			DBConfig::querySet('INSERT INTO `toplist` VALUES
				('.$value["idkp"].','.$value["num"].',"'.$value["title"].'","'.$value["date"].'","'.$value["rating"].'")');
		}
	}
	DBConfig::$ActualArrayFilms = DBConfig::queryGet("SELECT * FROM `toplist` order by num");
	$parser->insertToday();
}
