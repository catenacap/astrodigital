<?php

 
include('config.php');

@ini_set("output_buffering", "Off");
@ini_set('implicit_flush', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('max_execution_time', 48000);
 
if(isset($_GET['pages_read']) && $_GET['pages_read'] == 'all'){
	
	$selectDatabase = $db->query('SELECT COUNT(*) as total_count FROM `world_mineral_deposits`');
	$databaseInfo = $selectDatabase->fetch_array(MYSQLI_ASSOC);
	
	$totalPages = round($databaseInfo['total_count'] / 1000);
	
	if($totalPages == 0){
		$totalPages = 1;
	}
	
	$shell_commands = "";
	
	for($i = 0; $i < $totalPages; $i++){
		
		$shell_commands = 'wget https://www.xxx.com/astrodigital/cron_get_world_mineral_deposits.php?pg=' . $i . ' -O /dev/null &';
		
		shell_exec($shell_commands);
		
	} 
	
}

if(isset($_GET['pg']) && $_GET['pg'] >= '0'){
	
	if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/astrodigital/worldMineralDepositsDataCronLog" . $_GET['pg'] . ".txt")){
		
		if($_GET['pg'] == 0){
			$pageStart = 0;
			$worldMineralDepositsData = $db->query('SELECT `id` FROM world_mineral_deposits ORDER BY `id` ASC LIMIT ' . $pageStart . ', 1000');
		} else {
			
			$pageStart = ($_GET['pg'] * 1000) + 1;
			
			$worldMineralDepositsData = $db->query('SELECT `id` FROM world_mineral_deposits ORDER BY `id` ASC LIMIT ' . $pageStart . ', 1000');
		}
		
		if($worldMineralDepositsData->num_rows == 0){
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/astrodigital/worldMineralDepositsDataCronLog" . $_GET['pg'] . ".txt")){
				unlink('worldMineralDepositsDataCronLog' . $_GET['pg'] . '.txt');
			}
			
		}
		
		exit;
	} else {
		
		if($_GET['pg'] == 0){
			$pageStart = 0;
			$worldMineralDepositsData = $db->query('SELECT `id` FROM world_mineral_deposits ORDER BY `id` ASC LIMIT ' . $pageStart . ', 1000');
		} else {
			
			$pageStart = ($_GET['pg'] * 1000) + 1;
			
			$worldMineralDepositsData = $db->query('SELECT `id` FROM world_mineral_deposits ORDER BY `id` ASC LIMIT ' . $pageStart . ', 1000');
		}
		
		if($worldMineralDepositsData->num_rows > 0){
			
			file_put_contents('worldMineralDepositsDataCronLog' . $_GET['pg'] . '.txt', "running\n", FILE_APPEND);
			initWorldMineralDepositsData($pageStart, 1000);
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/astrodigital/worldMineralDepositsDataCronLog" . $_GET['pg'] . ".txt")){
				unlink('worldMineralDepositsDataCronLog' . $_GET['pg'] . '.txt');
				exit;
			}
			
		} else {
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/astrodigital/worldMineralDepositsDataCronLog" . $_GET['pg'] . ".txt")){
				unlink('worldMineralDepositsDataCronLog' . $_GET['pg'] . '.txt');
				exit;
			}
			
		}
		
	}
	
}

function initWorldMineralDepositsData($startPage, $showPages){
	
	global $db;
	
	$select_world_mineral_deposits = $db->query('SELECT `id`, `lat`, `long` FROM `world_mineral_deposits` LIMIT ' . $startPage . ', ' . $showPages);
	
	if($select_world_mineral_deposits->num_rows > 0){
		
		$dataArray = array();
		$i = 0;
		
		while($tableData = $select_world_mineral_deposits->fetch_array(MYSQLI_ASSOC)){
			
			$dataArray[$i]['world_mineral_deposits_id'] = $tableData['id'];
			
			$url = 'https://api.astrodigital.com/v2.0/search/?contains=' . $tableData['lat'] . ',' . $tableData['long'];
			
			$dataArray[$i]['url'] = $url;
			
			$i++;
			
		}
		
		$total_pages = count($dataArray);
		$instances = 20;
		$chunks = ceil($total_pages / $instances);
		
		start_execution($total_pages, $instances, $chunks, $dataArray);
		
	}
	
}

function start_execution($total_pages, $instances, $chunks, $dataArray){
	
  //var_dump($total_pages, $instances, $chunks); exit;
  
  for($chk = 1; $chk <= $chunks; $chk++){
  	
    $urls = array();
    $start = (($chk - 1) * $instances) + 1;
    
    if($chk == 1){
    	$end = $chk * $instances;
    } else if($chk == $chunks){
      $end = $total_pages;
    } else {
      $end = $chk * $instances;
    }

    for($page = $start; $page <= $end; $page++){
      $urls[] = $dataArray[$page];
    }
    
    //echo "<pre>"; print_r($urls); exit;
    
    // send multiple request at the same time.
    multiRequest($urls, $chk);
    
    unset($urls);
    
  }
  
}

function multiRequest($data, $iteration, $options = array()) {
	
  // array of curl handles
  $curly = array();
  
  // data to be returned
  $result = $apiKeys = array();
  
  // Generate a random variable.
  ${"data".$iteration} = $data;
  $world_mineral_deposits = $data;
  unset($data);
  
  // multi handle
  $mh = curl_multi_init();
  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach (${"data".$iteration} as $id => $d) {
  	
    $curly[$id] = curl_init();
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL, $url);
    curl_setopt($curly[$id], CURLOPT_HEADER, 0);
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curly[$id], CURLOPT_TIMEOUT, 40);
		curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curly[$id], CURLOPT_SSL_VERIFYHOST, false);
		
    // post?
    if (is_array($d)) {
      if (!empty($d['post'])) {
        curl_setopt($curly[$id], CURLOPT_POST,       1);
        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
      }
    }
    
    // extra options?
    if (!empty($options)) {
      curl_setopt_array($curly[$id], $options);
    }
    
    curl_multi_add_handle($mh, $curly[$id]);
    
  }
  
  // execute the handles
  $running = null;
  
  do {
    @curl_multi_exec($mh, $running);
  } while($running > 0);
  
  // get content and remove handles
  foreach($curly as $id => $c) {
    $result[$id] = curl_multi_getcontent($c);
    $worldMineralDepositsIDs[$id] = $world_mineral_deposits[$id]['world_mineral_deposits_id'];
    curl_multi_remove_handle($mh, $c);
  }
  
  // all done
  curl_multi_close($mh);
  
  // Extrat the data.
  foreach($result as $key => $world_mineral_deposits_data){
  	
  	extract_data($world_mineral_deposits_data, $worldMineralDepositsIDs[$key]);
  	
  }
  
  // clear the resources.
  unset($result);
  unset($apiKeys);
  unset(${"data".$iteration});
  
}

function extract_data($responseData, $world_mineral_deposits_id){
	
	global $db, $db1_k;
	
	$json = json_decode($responseData);
	
	if(isset($json->results) && count($json->results) > 0){
		
		foreach($json->results as $json_data){
			
			if(isset($json_data->browseURL)){
				
				$image_name = "world_mineral_deposits_" . $world_mineral_deposits_id . '_' . $json_data->date . '.jpg';
				$image_url = "https://www.dkdddje884487577rf7rec8r7c7rc7r7xmsx.com/astrodigital/astrodigital_world_mineral_deposits/" . $image_name;
				
				$image_server_response = file_get_contents('https://www.dkdddje884487577rf7rec8r7c7rc7r7xmsx.com/astrodigital/get_world_mineral_deposits_image.php?world_mineral_deposits_image_name=' . $image_name . '&image_url=' . $json_data->browseURL);
				
				if($image_server_response != 'error'){
					
					$decode_color_extraction = json_decode($image_server_response);
					$serialize_colors = serialize($decode_color_extraction);
					
					$db_table_name = "world_mineral_deposits_astrodigital_" . $world_mineral_deposits_id;
					
					// Create dynamic databaes table into DB 5
					$db1_k->query('CREATE TABLE IF NOT EXISTS `' . $db_table_name . '` (
										  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
										  `astrodigital_date` VARCHAR(20) NULL,
										  `xxx_image_url` VARCHAR(500) NULL,
										  PRIMARY KEY (`id`)
										) ENGINE=INNODB AUTO_INCREMENT=1 CHARSET=utf8');
										
					$db1_k->query("SHOW COLUMNS FROM " . $db_table_name . " LIKE 'colors_extracted'");
					
					if($db1_k->num_rows == 0){
						$alter = "ALTER TABLE " . $db_table_name . " ADD `colors_extracted` TEXT NULL"; 
						$db1_k->query($alter); 
					}
					
					// Check for duplicate entry
					$astrodigital_date = $db1_k->real_escape_string(trim($json_data->date));
					$selectData = $db1_k->query('SELECT * FROM `' . $db_table_name . '` WHERE `astrodigital_date` = "' . $astrodigital_date . '" AND `xxx_image_url` = "' . $image_url . '"');
					
					if($selectData->num_rows == 0){
						
						$db1_k->query('INSERT INTO `' . $db_table_name . '` (`astrodigital_date`, `xxx_image_url`, `colors_extracted`) VALUES ("' . $astrodigital_date . '", "' . $image_url . '", "' . $db1_k->real_escape_string($serialize_colors) . '")');
						
					}
					
				}
				
			}
			
		}
		
	}
	
}
 
?>