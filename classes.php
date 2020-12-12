<?
class Parser extends AbstractClass implements Template
{
	//today day + getter, setter
	//private $today;
	public function setToday($val)
	{
		$this->today = $val;
	}
	public function getToday()
	{
		return $this->today;
	}
	//lastParser day + getter, setter
	//private $lastParser;
	public function setLastParser($val)
	{
		$this->lastParser = $val;
	}
	public function getLastParser()
	{
		return $this->lastParser;
	}
	private $URL = 'https://www.kinopoisk.ru/popular/films/?tab=all';

	function __construct()
	{
		$this->setToday(date("d.m.y"));
		$this->setLastParser(file_get_contents("time"));
	}
	
	public function executePars()
	{
		$html = $this->parsUrl($this->URL);
		$list = $this->popularRegular($html);
		return $list;
	}
	public function sortOldList($oldList, $newList){
		$deleteList = array();
		$updateList = array();
		foreach ($oldList as $idkp => $value) {
			if(array_key_exists($idkp,$newList))
			{
				unset($newList[$idkp]);
				if($value != $newList[$idkp])
					$updateList[$idkp] = $newList[$idkp];
			}else{
				$deleteList[$idkp] = $value;
			}
		}
		return array(
			"toDelte"=>$deleteList, 
			"toUpdate"=>$updateList,
			"toInsert"=>$newList
		);
	}
	public function popularRegular ($content){
		preg_match_all ('#href="\/film\/(\d+?)\/"\s+class="selection-film-item-meta__link"#', $content, $idkp_mass);
		preg_match_all ('#class="selection-film-item-meta__name">(.*?)<#', $content, $title_mass);
		preg_match_all ('#class="selection-film-item-meta__original-name">(.*?)<#', $content, $date_eng_mass);	
		preg_match_all ('#class="rating__value.*?>(.*?)<.*?class="rating__count".*?>(.*?)<#', $content, $rating_mass);
		preg_match_all ('#class=".*?film-item-rating-position__diff_sign_(.*?)">(.*?)<#', $content, $pos_mass);

		# разбиваю массив с датой и англ.названием
		foreach ($date_eng_mass[1] as $key => $angl_date) {
			$mass_date_full[$key]  = explode(",", $angl_date,2);
		}	

		# разбиваю массив с датой и англ.названием и чищу от мусора
		foreach ($mass_date_full as $key => $val) {
			if ($val[1]) {
				$val[1] = trim (str_replace ('The, ', '', $val[1]));
				$eng_mass[$key] = $val[0];
				$date_mass[$key] = $val[1];
			} else {
				$eng_mass[$key] = '';
				$date_mass[$key] = $val[0];			
			}
		}	

		# собираю массив
		foreach ($idkp_mass[1] as $key => $idkp) {
			$mass_full[$idkp]['idkp']     = $idkp;
			$mass_full[$idkp]['num']      = $key + 1;
			$mass_full[$idkp]['title']    = $title_mass[1][$key];
			$mass_full[$idkp]['date']     = $date_mass[$key];
			//$mass_full[$idkp]['title_en'] = $eng_mass[$key];		
			$mass_full[$idkp]['rating']   = $rating_mass[1][$key];
			// $mass_full[$idkp]['posneg']   = $pos_mass[1][$key];
			// $mass_full[$idkp]['posnum']   = $pos_mass[2][$key];
		}	
		return $mass_full;
	}
	public function parsUrl ($url) {
	    if ( $curl = curl_init() ) {
	        curl_setopt($curl, CURLOPT_URL, $url);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/".rand(10000000, 30000000)." Firefox/1.0.7");
	        curl_setopt($curl, CURLOPT_REFERER, "https://www.kinopoisk.ru/");
	        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($curl, CURLOPT_ENCODING, "");
	        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 6);
	        curl_setopt($curl, CURLOPT_TIMEOUT, 9);
	        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        $content = curl_exec($curl);
	        curl_close($curl);
	    } else
	        $content = file_get_contents($url);	
		return $content;
	}
}
interface Template{
	public function parsUrl($content);
	public function popularRegular($url)
}
abstract class AbstractClass{
	private $today;
	private $lastParser;
	abstract protected function insertToday();
	abstract protected function isLastParsToday();
}
?>