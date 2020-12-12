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
?>
<!DOCTYPE HTML>
<html>
	<head>
	<meta charset="utf-8">
	<title>ТОП 50 популярных</title>
</head>
<body>
	<table border="1">
		<caption><a href="https://www.kinopoisk.ru/popular/films/" target="_blank">ТОП 50 популярных</a></caption>
		<tr>
			<th>№</th>
			<th>ИДКП</th>
			<th>Название</th>
			<th>Дата</th>
			<th>Рейтинг</th>
		</tr>
		<?
		foreach (DBConfig::$ActualArrayFilms as $value) {
			$string = "<tr>";

			$string .= "<td>".$value["num"]."</td>";
			$string .= "<td>".$value["idkp"]."</td>";
			$string .= "<td>".$value["title"]."</td>";
			$string .= "<td>".$value["date"]."</td>";
			$string .= "<td>".$value["rating"]."</td>";

			$string .= "</tr>";
			echo $string;
		}
		?>
	</table>
</body>
</html>