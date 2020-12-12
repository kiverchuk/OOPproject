<?
class Parser //extends AbstractClass implements Template
{
	
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