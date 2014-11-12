<?php
namespace controller;
  //Dependencies
  require_once("./src/model/m_course.php");
  require_once("./src/model/m_xpath.php");
  require_once("./src/view/v_curl.php");
  require_once('./Settings.php');
  
  
/**
 * Navigation view for a simple routing solution.
 */
class Curl {
		
	private $htmlBody, $url;
	
	public function __construct() {	;
      $this->view = new \view\Curl();
    }
	
	/**
	 * returns value to HTMLView.
	 */
	public function doControll() {

		try {
								
				#only fetch courses ecery 5 minutes
			
				$string = file_get_contents("files/courses.json");
				if(!empty($string)) {
					$json_content = json_decode($string,true);
					$latestScrape = $json_content['timeStamp'];
					$time = false;
					if ($time ==true){ //time() - $latestScrape > 300) { //<-- true if time passed is less than 5 minutes	//TODO change > to <				 
						$this->htmlBody = @file_get_contents("files/htmlBody.txt");
					}
					
					else {  //do web scraping! 
						$courses = $this->fetchCourses();
						
						$coursesArrayObject = new \ArrayObject($courses);
						$coursesArrayObject->asort();
						
						$this->htmlBody = $this->view->showAllCourses($coursesArrayObject);
						
						$this->view->toJson($coursesArrayObject);
					}
					
				}
				
				return $this->htmlBody;
						 
			
		} catch (\Exception $e) {

			error_log($e->getMessage() . "\n", 3, \Settings::$ERROR_LOG);
			if (\Settings::$DO_DEBUG) {
				throw $e;
			} else {
				header('Location: /' . \Settings::$ROOT_PATH. '/error.html');
				die();
			}
		}
	}
	/*
	 * @return array (with course-objects containing all information)
	 */
	public function fetchCourses() {
	
				$courseArray = array();	
				$numbers = array();
				$url =  "http://coursepress.lnu.se/kurser/";
				
				$xpathStartPage = new \model\XPATH($url);
				$pages = $xpathStartPage->query('//div[@id = "blog-dir-pag-top"]//a[@class="page-numbers"]');
				
				foreach ($pages as $page) {
					array_push($numbers, $page->nodeValue);
				}				
				
				$pages = max($numbers);
				
				//Do web scraping for every page that contains courses
				for ($i= 1; $i<=$pages; $i++)	 {
						
					 $substring =  "?bpage=".$i ;
					
					//xpath for courses	
					$xpathCourses = new \model\XPATH($url . $substring);
				
					//fetching all a-tags which includes course name and course url
						
					$courseData = $xpathCourses->query('//ul[@id = "blogs-list"]//div[@class = "item-title"]/a');
						//throw new \Exception(var_dump("hupp"));
					foreach ($courseData as $item) {			
						
						if (strpos($item->getAttribute("href") ,'kurs') !== false) {
								
							//create new course object	
							$course = new \model\Course();
							
							//name and url
							$course->name = $item->nodeValue;
							$course->url = $item->getAttribute("href");
			
							//course code
							$course->code = $this->getSingleElement($course->url, '//div[@id = "header-wrapper"]//a[3]');
							
							//course information
							$course->info = $this->getSingleElement($course->url, '//div[@class = "entry-content"]//p');
							
							//headline latest post
							$course->headlineLatestPost = $this->getSingleElement($course->url, '//section//article//h1[1]');
							
							//date and time - latest post
							$date = $this->getSingleElement($course->url, '//section//article//p[@class ="entry-byline"]');						
							preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $date, $match);
		
							if (!empty($match[0])){
								$course->dateLatestPost =$match[0];
							}else {$course->dateLatestPost =$date;}
							
							//author - latest post
							$author = $this->getSingleElement($course->url, '//section//article//p[@class ="entry-byline"]/strong');	
							
							if (!empty($match[0])){
								
								$course->authorLatestPost = $author;
							}else {$course->authorLatestPost = $date;}
							
							
							//push courses into array 
							array_push($courseArray, $course);
							
						}
				} 												
		}
			return $courseArray; 
	}

	public function getSingleElement($url, $path) {
		$xpathCourseStartpage = new \model\XPATH($url);			
		$element = $xpathCourseStartpage->querySingleItem($path);
		if ($element != null){
			return $element->nodeValue;	
		}else {
			return "(Saknas information).";
		}
	}
	
}
