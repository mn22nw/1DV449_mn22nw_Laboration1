<?php 
namespace model;

require_once("src/model/m_course.php");

class XPATH {
	public $dom, $xpath;
	private static $dateTime = 'dateTime';
	
	public function __construct($url) {
		$html = $this->_curl($url);
		$this->dom = new \DOMDocument();
		@$this->dom->loadHTML($html);		//'<meta http-equiv="content-type" content="text/html; charset=utf-8">' .
		$this->xpath = new \DOMXPath($this->dom);
	}
	
	/*
	 * @returns array of the objects (html-tags) that are being fetched withing the query
	 */
	public function query($q){
		
			$items = $this->xpath->query($q);		

		return $items;
	}
	public function querySingleItem($q){
			$items = $this->xpath->query($q);
				
				foreach ($items as $item) {
					return $item;
				};
	}

	
	private function _curl($url) {
		// Initialising cURL 
		ini_set('max_execution_time', 300);
		$ch = curl_init($url);		
		
		if (FALSE === $ch)
        throw new \Exception('failed to initialize');
	//	$headers = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8');//("Content-Type: application/x-www-form-urlencoded; charset:0B80");
		
		$options = Array(
			//CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => TRUE,  
            CURLOPT_AUTOREFERER => TRUE,  
            CURLOPT_USERAGENT =>'mn22nw',
            CURLOPT_URL => $url 
        );
		
        curl_setopt_array($ch, $options);   
        $data = curl_exec($ch); 
      	if (!$data)
        throw new \Exception(curl_error($ch), curl_errno($ch));  
   
		// Closing cURL 
		curl_close($ch); 

		return $data;
	}
	
}
