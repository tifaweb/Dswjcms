<?php
    function pre($arr){
       echo "<pre>";
	   print_r($arr);
	   echo "</pre>";
	}
  function RJson($arr){
		    header('Content-Type: application/json');
		    echo json_encode ($arr); 
			exit;
  }	
  
  function bfb($fl){
	  return ($fl * 100) ."%";
  }
	
	
?>