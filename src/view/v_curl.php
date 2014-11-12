<?php
  namespace view;

  require_once("src/controller/c_curl.php");

  class Curl {

	/*
	 * @return - array with html for courses 
	 * 
	 ---------------------------------*/
	public function showAllCourses($courses) {
		$html = "";
		$html .= "<h1> Kurser</h1>";
		$html .= "<div id='json'><a href='files/courses.json' target= ' _blank'>Visa json</a></div>";
		$html .= "<ul>";
		foreach ($courses as $course) {
			$html .= "<li>";	
			$html .= "<a href='".$course->url."'  target= ' _blank'><h2>". $course->name ." - ".$course->code. "</h2></a>";	
			$html .= "<p><span>Kursinfo: </span>". $course->info ."</p>";	
			$html .= "<div id='news'>";
			$html .= "<p><span>Senaste posten: </span>". $course->headlineLatestPost ."</p>"; 
			$html .= "<p><span>Skapat: </span>". $course->dateLatestPost ."</p>"; 
			$html .= "<p><span>Skrivet av: </span>". $course->authorLatestPost ."</p>"; 
			$html .= "</div></li>";		
			}
		$html .= "</ul>";
		//save html to file
		$fp = fopen('files/htmlBody.txt', 'w');
		fwrite($fp,$html);
		fclose($fp);
		
		return $html;
	}
	
	public function toJson($courses) {
		$numberOfCourses = count($courses);					
		$jsonArray = array('Number of courses' => $numberOfCourses, 'timeStamp' =>time(), 'Courses' => $courses);
		$fp = fopen('files/courses.json', 'w');
		fwrite($fp,json_encode($jsonArray, JSON_PRETTY_PRINT));
		fclose($fp);
			
	}

  }
