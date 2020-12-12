<?
class DBConfig{
	public static $DBName = "miniproiect";
	public static $DBUser = "miniuser";
	public static $DBPass = "MiniUserPass";
	public static $Conn;
	public static $ActualArrayFilms;
	public static function connect(){
		return DBConfig::$Conn = mysqli_connect("localhost", DBConfig::$DBUser, DBConfig::$DBPass, DBConfig::$DBName);
	}
	public static function queryGet($string){
		$data = DBConfig::$Conn->query($string);
		$array = array();
		while( $line = mysqli_fetch_assoc($data) )
		{
			$array[$line['idkp']] = $line;
		}
		return $array;
	}
	public static function querySet($string){
		return DBConfig::$Conn->query($string);
	}
}
?>