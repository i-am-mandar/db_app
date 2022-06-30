<?php
//require_once "config.php";


function get_search($get)
{
	global $debug;
	
	//data_time in php = Y-m-d H:i:s and clear text = yyyy-MM-dd HH:mm:ss
	//uuid=abc&from_station=frankfurt&to_station=berlin&date_time=2022-06-15 09:01:05&class=2&no_of_passenger=2&special_service_flag=true&seats=2&food_service=true&luggage_service=false
	$request = array();
	$request["uuid"] = $get["uuid"];
	$request["from_station"] = $get["from_station"];
	$request["to_station"] = $get["to_station"];

	$from_station = get_station_details($request["from_station"]);
	$to_station = get_station_details($request["to_station"]);	
	
	$connection = get_connection($from_station,$to_station);
		
	$connection = get_train_path($from_station, $to_station, $connection);
	
	//echo var_dump($connection);
	
	if($debug > 1 && $connection != NULL)
	{
		echo "from station: " . $from_station["station"]. "\nto station: " .$to_station["station"] . PHP_EOL;
		
		echo "\nid " . $connection["id"] . "\n\tfrom: " . $connection["from_station"]. "\n\tto: " . $connection["to_station"]. "\n\ttrain_table: " . $connection["train_table"]. "\n\tpath: " . $connection["path"] . "\n\tprice: " . $connection["price"].PHP_EOL;
		
		echo "\nsearch\n\tflip= ". $connection["flip"] . "\n\tstart_value= " . $connection["start_value"] . "\n\tis_via= " .$connection["is_via"]. "\n\tis_connection= " . $connection["is_connection"] . PHP_EOL;
		
		if($connection["is_via"])
			echo "\nid " . $connection["via"]["id"] . "\n\tis_via: 1\n\tfrom: " . $connection["via"]["from_station"]. "\n\tto: " . $connection["via"]["to_station"]. "\n\ttrain_table: " . $connection["via"]["train_table"]. "\n\tpath: " .$connection["via"]["path"] . "\n\tprice: " . $connection["via"]["price"].PHP_EOL;
	}
	
	//check available train in timetable and seats remaining - the trains should be 3 scheduled after the time given
	//echo var_dump($train_data_time);
	//year, month, day, hour, minute, seconds
	$request["date_time"] = $get["date_time"];
	$train_data_time = date_parse_from_format("Y-m-d H:i:s", $request["date_time"]);
	$connection = get_train_schedule($from_station, $to_station, $connection, $request["date_time"]);
	
	//calc cost as per class and time remaining for whole booking
	$request["class"] = $get["class"];
	$request["no_of_passenger"] = $get["no_of_passenger"];
	$connection = get_additional_cost($request["class"], $request["no_of_passenger"],$connection);
	
	//special_service_flag adds to cost
	$request["special_service_flag"] = $get["special_service_flag"];
	if($request["special_service_flag"] == true)
	{
		$request["seats"] = $get["seats"];
		$request["food_service"] = $get["food_service"];
		$request["luggage_service"] = $get["luggage_service"];
	}
	
		
	//get all details back as per reservation table and we are done
	$json_connection = json_encode($connection);
	$json_from_station = json_encode($from_station);
	$json_to_station = json_encode($to_station);
	$return_connection = array();
	
	$return_connection["uuid"] = $request["uuid"];
	$return_connection["status"] = "complete";
	$return_connection["from_station_name"] = $from_station["station"];
	$return_connection["from_station_id"] = $from_station["station_id"];
	$return_connection["to_station_name"] = $to_station["station"];
	$return_connection["to_station_id"] = $to_station["station_id"];
	$return_connection["train_date"] = date('Y-m-d', strtotime($request["date_time"]));
	//$return_connection["train_time"] = date('H:i:s', strtotime($request["date_time"]));
	$return_connection["class"] = (int)$request["class"];
	$return_connection["amount"] = $connection["price"];
	$return_connection["total_amount"] = $connection["total_price"];
	$return_connection["no_of_passenger"] = (int)$request["no_of_passenger"];
	$return_connection["special_service_flag"] = (bool)$request["special_service_flag"];
	if($request["special_service_flag"] == true)
	{
		$return_connection["seats"] = (int)$request["seats"];
		$return_connection["food_service"] = (bool)$request["food_service"];
		$return_connection["luggage_service"] = (bool)$request["luggage_service"];
	}
	$return_connection["connecting"] = $connection["is_via"];
	if($connection["is_via"] == 1)
	{
		$return_connection["connecting_station"] = $connection["via_connection"];
		$return_connection["connecting_station_id"] = (int)$connection["via_connection_id"];
	}
	$return_connection["is_connection"] = $connection["is_connection"];
	if($connection["is_connection"] == 1)
	{
		$return_connection["train_list_count"] = $connection["train_list_count"];
		$return_connection["path"] = $connection["path"];
		$return_connection["train_list"] = $connection["train_list"];
		
		if($connection["is_via"] == 1)
		{
			$return_connection["via"]["train_list_count"] = (int)$connection["via"]["train_list_count"];
			$return_connection["via"]["path"] = $connection["via"]["path"];
			$return_connection["via"]["train_list"] = $connection["via"]["train_list"];
		}
	}
	$json_return_connection = json_encode($return_connection);
	
	//echo $json_connection;
	echo $json_return_connection;
}

function get_station_details($in_station)
{
	global $conn, $debug;
	
	$station = array();
	$sql = "SELECT station_id, station, main, via, region, start FROM stations WHERE station='". $in_station ."';";
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) == 1) 
	{
		$station = mysqli_fetch_assoc($result);
		if($debug > 1)
		{
			echo "\nstation_id: " . $station["station_id"] . "\n\tname: " . $station["station"]. "\n\tmain: " . $station["main"]. "\n\tvia: " . $station["via"]. "\n\tregion: " . $station["region"]. "\n\tstart: " . $station["start"]. "\n";
		}
		return $station;
	} 
	else
	{
		$station = NULL;
	}	
	
	return $station;
}


function get_connection($from_station, $to_station)
{
	global $conn, $debug;
	$flip = 0;
	$is_via = 0;
	$i = 0;
	$is_connection = 1;
	$start_value = 0;
	$in_rows = array();
	//echo var_dump($from_station);
	$connection = array();
	
	//case for both stations are in same region
	if($from_station["region"] == $to_station["region"])
	{
		$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $from_station["region"] ."' OR to_station='". $to_station["region"]."';";
		$result = mysqli_query($conn, $sql);
		$rows = mysqli_num_rows($result);
		$connections[$rows] = array();
		//echo "THIS" . PHP_EOL;
        if (mysqli_num_rows($result) > 0) 
		{
          // output data of each row
			while($connections[$i] = mysqli_fetch_assoc($result)) 
			{
				if($debug > 1)
				{
					echo "\nrow: ".$rows. "\nno: " . $i . "\n\tid " . $connections[$i]["id"] . "\n\tfrom: " . $connections[$i]["from_station"]. "\n\tto: " . $connections[$i]["to_station"]. "\n\ttrain_table: " . $connections[$i]["train_table"]. "\n";
				}
				$i++;
			}
			
			//if to_station is start station then flip
			//if($to_station["start"] > 0)
			//	$flip = 1;
		
			$in_rows2 = array();
			//loop thru all train_table and get fair decide if flip is required
			
			for($i = 0; $i < $rows; $i++)
			{
				$sql = "SELECT id, station_id, starts, price, path FROM ".$connections[$i]["train_table"]." WHERE starts='". $from_station["station"]."' OR starts='".$to_station["station"]."';";
				//echo $sql . PHP_EOL;
				$result = mysqli_query($conn, $sql);
				if (mysqli_num_rows($result) == 2)
				{
					$connection = $connections[$i];
					$in_rows = mysqli_fetch_assoc($result);
					$in_rows2 = mysqli_fetch_assoc($result);
					break;
				}
				
			}
			
			$is_connection = 1;
			
			//calc cost and check if flip			
			if($from_station["station"] == $in_rows["starts"])
			{
				$p1 = $in_rows["price"];
				$p2 = $in_rows2["price"];
				if($p1 > $p2)
					$flip = 1;
			}
			else if($from_station["station"] == $in_rows2["starts"])
			{
				$p1 = $in_rows["price"];
				$p2 = $in_rows2["price"];
				if($p1 < $p2)
					$flip = 1;
			}				
			
			$connection["start_value"] = $start_value; 
			$connection["flip"] = $flip; 
			$connection["is_via"] = $is_via; 
			$connection["is_connection"] = $is_connection;
        } 
		else 
		{
			$connection = NULL;
        }
	}
	//case for stations are in different region
	else
	{
		//$connection = array();
		$flip = 0;
		if(($from_station["start"] > 0 ) && ($to_station["start"] > 0))
			$start_value = $from_station["start"] - $to_station["start"];
		else if($from_station["start"] > 0 ) 
			$start_value = $from_station["start"];
		else if ($to_station["start"] > 0)
			$start_value = -1;
		else
			$start_value = 0;
		
		if($start_value < 0)
			$flip = 1;
		
		if($flip && $start_value != 0)
		{
			//flip since to_station is start station and get train_table
			$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $to_station["region"] ."' AND to_station='". $from_station["region"]."';";
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) == 1) 
			{
				$is_connection = 1;
				$connection = mysqli_fetch_assoc($result);
				$connection["start_value"] = $start_value; 
				$connection["flip"] = $flip; 
				$connection["is_via"] = $is_via; 
				$connection["is_connection"] = $is_connection;
			}
			else
			{
				//case is a connecting train, more work to do
				$flip = 0;
				$is_via = 1;
				$is_connection = 0;
			}
		}
		else if(($flip == 0) && ($start_value > 0))
		{
			//from_station is start station and get train_table
			$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $from_station["region"] ."' AND to_station='". $to_station["region"]."';";
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) == 1) 
			{
				$is_connection = 1;
				$connection = mysqli_fetch_assoc($result);
				$connection["start_value"] = $start_value; 
				$connection["flip"] = $flip; 
				$connection["is_via"] = $is_via; 
				$connection["is_connection"] = $is_connection;
			}
			else
			{
				//case is a connecting train, more work to do
				$flip = 0;
				$is_via = 1;
				$is_connection = 0;
			}
		}
		else
		{
			//we dont now the start station but try to get train_table if no data then flip
			$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $from_station["region"] ."' AND to_station='". $to_station["region"]."';";
			
			//erfurt to munich fails
			//connection should be c-c-s
			//echo $sql .PHP_EOL;

			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) == 1) 
			{
				$is_connection = 1;
				$connection = mysqli_fetch_assoc($result);
				$connection["start_value"] = $start_value; 
				$connection["flip"] = $flip; 
				$connection["is_via"] = $is_via; 
				$connection["is_connection"] = $is_connection;
				
				$sql = "SELECT * from ".$connection["train_table"]." WHERE starts='".$from_station["station"]."'";
				$result = mysqli_query($conn, $sql);
				if (mysqli_num_rows($result) == 0)
				{
					$is_via = 1;
					$connection["is_via"] = $is_via;
					//break;
				}					
			}
			//try fliping and check
			else
			{
				$is_connection = 1;
				$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $to_station["region"] ."' AND to_station='". $from_station["region"]."';";
				$result = mysqli_query($conn, $sql);
				if (mysqli_num_rows($result) == 1) 
				{
					$connection = mysqli_fetch_assoc($result);
					$flip = 1;
					$connection["start_value"] = $start_value; 
					$connection["flip"] = $flip; 
					$connection["is_via"] = $is_via; 
					$connection["is_connection"] = $is_connection;
				}
				//case is a connecting train, more work to do
				else
				{
					$flip = 0;
					$is_via = 1;
					$is_connection = 0;
				}
			}
		}
		
		if($is_via == 1)
		{
			$is_connection = 1;
			//case is a connecting train, more work done here
			//http://localhost/db_app/index.php/train/booking?uuid=abc&from_station=hamburg&to_station=munich&date_time=20220615090105&class=2&no_of_passenger=2&special_service_flag=true&seat_no=2&food_service=true&luggage_service=false
			$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='". $from_station["region"] ."' OR to_station='".$from_station["region"]."';";
			$result = mysqli_query($conn, $sql);
			$rows = mysqli_num_rows($result);
			$connections[$rows] = array();
			$i = 0;
			if (mysqli_num_rows($result) > 0) 
			{
				// output data of each row
				while($connections[$i] = mysqli_fetch_assoc($result))
				{
					if($connections[$i]["from_station"] != $from_station["region"])
					{
						$flip = 1;
						$connections[$i]["flip"] = 1;
					}
					else if($connections[$i]["from_station"] == $from_station["region"])
					{
						$flip = 1;
						$connections[$i]["flip"] = 1;
					}
					else
					{
						$flip = 0;
						$connections[$i]["flip"] = 0;
					}
					if($debug > 1)
					{
						echo "\nrow: ".$rows. "\nno: " . $i . "\n\tid " . $connections[$i]["id"] . "\n\tfrom: " . $connections[$i]["from_station"]. "\n\tto: " . $connections[$i]["to_station"]. "\n\ttrain_table: " . $connections[$i]["train_table"]. "\n\tflip: " . $connections[$i]["flip"]  . "\n";
					}
					$i++;
				}


				for($i = 0; $i < $rows; $i++)
				{
					$sql = "SELECT id, station_id, starts FROM ".$connections[$i]["train_table"]." WHERE starts='" .$from_station["station"]."';";
					$result = mysqli_query($conn, $sql);
					if (mysqli_num_rows($result) == 1) 
					{
						//mysqli_fetch_assoc($result);
						//echo $sql .PHP_EOL;
						$sql = "SELECT id, from_station, to_station, train_table FROM connection WHERE from_station='".$connections[$i]["from_station"]."' AND to_station='".$to_station["region"]."';";
						$result1 = mysqli_query($conn, $sql);
						if (mysqli_num_rows($result1) == 1) 
						{
							//echo $sql .PHP_EOL;
							$connection = $connections[$i];
							$in_rows = mysqli_fetch_assoc($result1);
						}
					}
				}
				
				

				$connection["start_value"] = $start_value; 
				$connection["flip"] = $flip; 
				$connection["is_via"] = $is_via; 
				$connection["is_connection"] = $is_connection;
				$connection["via"] = $in_rows;
			    //echo var_dump($in_rows) . PHP_EOL;
			}					
		}
			
	}
	
	if($debug > 1 && $is_connection == 1)
	{
		echo "\nid " . $connection["id"] . "\n\tfrom: " . $connection["from_station"]. "\n\tto: " . $connection["to_station"]. "\n\ttrain_table: " . $connection["train_table"]. "\n";
		
		echo "\nsearch\n\tflip= ". $flip . "\n\tstart_value= " . $start_value . "\n\tis_via= " .$is_via. "\n\tis_connection= " . $is_connection . PHP_EOL;
		
		if($is_via)
			echo "\nid " . $connection["via"]["id"] . "\n\tis_via: 1\n\tfrom: " . $connection["via"]["from_station"]. "\n\tto: " . $connection["via"]["to_station"]. "\n\ttrain_table: " . $connection["via"]["train_table"]. "\n";
	}
	
	return $connection;
	
}

function get_train_path($from_station, $to_station, $connection)
{
	global $conn, $debug;
	$path = "";
	$price = 0;
	//check if its via or not
	//if via do double work else process mentioned below should work
	if($connection["is_via"] == 1)
	{
		// do remember in is_via the 2nd path is never a flip but 1st is always a flip
		$easy = get_easy_path_price($from_station["station"], $to_station["station"], $connection["train_table"], $connection["flip"], true);
		$easy_via = get_easy_path_price($easy["last_station"], $to_station["station"], $connection["via"]["train_table"], 0, false);
		
		$connection["via_connection"] = $easy["last_station"];
		$connection["via_connection_id"] = $easy["last_station_id"];
		$connection["path"] = $easy["path"];
		$connection["price"] = $easy["price"];
		
		$connection["via"]["path"] = $easy_via["path"];
		$connection["via"]["price"] = $easy_via["price"];
	}
	else
	{
		$easy = get_easy_path_price($from_station["station"], $to_station["station"], $connection["train_table"], $connection["flip"], false);
		$connection["path"] = $easy["path"];
		$connection["price"] = $easy["price"];		
	}
	if($debug > 1)
		echo "\ntrain path\n\tpath: " . $easy["path"] . "\n\tprice: " . $easy["price"]. PHP_EOL;
	if(($debug > 1) && ($connection["is_via"] == 1))
		echo "\ntrain path\n\tpath: " . $easy_via["path"] . "\n\tprice: " . $easy_via["price"]. PHP_EOL;
	
	return $connection;

}

function get_easy_path_price($from_station, $to_station, $train_table, $flip, $is_via)
{
	global $conn, $debug;
	$path = "";
	$price = 0;
	$row = array();
	
	//$train_table = $connection["train_table"];
		
	if($flip == 1)
		$sql = "SELECT id, station_id, starts, price, path FROM ". $train_table." WHERE starts='". $from_station."';";
	else
		$sql = "SELECT id, station_id, starts, price, path FROM ". $train_table." WHERE starts='". $to_station."';";
	//echo $sql .PHP_EOL;
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) == 1)
		$row = mysqli_fetch_assoc($result);
	
	$conditional_price  = $row["price"];
	
	$sql = "SELECT id, station_id, starts, price, path FROM ". $train_table." WHERE price<=".$conditional_price.";";
	$result = mysqli_query($conn, $sql);
	if(mysqli_num_rows($result) > 0)
	{
		if($flip == 1)
		{
			//$path="start";
			$first = true;
			while($row = mysqli_fetch_assoc($result))
			{
				$price = $price + $row["price"];
				//$path = $path . "-" . $row["starts"];
				$path = sprintf('%1$s-%2$s', $row["starts"], $path);
				if($first == true)
				{
					$last_station = $row["starts"];
					$last_station_id = $row["station_id"];
					$first = false;
				}
			}
			$path = "start-" .$path . "end";
		}
		else
		{
			$path="start";
			while($row = mysqli_fetch_assoc($result))
			{
				$price = $price + $row["price"];
				$path = $path . "-" . $row["starts"];
				$last_station = $row["starts"];
			}
			$path = $path . "-end";
		}
		
		//echo "\neasy last station: ". $last_station . PHP_EOL;
		if($debug > 1)
			echo "\neasy train path\n\tpath: " . $path . "\n\tprice: " . $price. PHP_EOL;
		
	}
	
	$easy["path"] = $path;
	$easy["price"] = $price;
	if($is_via)
	{
		$easy["last_station"] = $last_station;
		$easy["last_station_id"] = $last_station_id;
	}
	
	return $easy;
}

function get_train_schedule($from_station, $to_station, $connection, $train_data_time)
{
	global $conn, $debug;
	
	
	//get offset time from train_table for from station
	$train_table = $connection["train_table"];
		
	if($connection["flip"] == 1)
		$sql = "SELECT id, station_id, starts, price, path, down FROM ". $train_table." WHERE starts='". $from_station["station"]."';";
	else
		$sql = "SELECT id, station_id, starts, price, path, up FROM ". $train_table." WHERE starts='". $from_station["station"]."';";
	
		
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) == 1)
		$row = mysqli_fetch_assoc($result);
	
	if($connection["flip"] == 1)
		$offset_time = $row["down"];
	else
		$offset_time = $row["up"];
	
	$connection["requested_date_time"] = $train_data_time;
	//echo "\nrequested_date_time: ".$connection["requested_date_time"] . PHP_EOL;
	
	//calc start time of train as
	//train_start_time = train_data_time - offset
	$train_start_time = date('Y-m-d H:i:s', strtotime($train_data_time. ' -'. $offset_time.' minutes'));	
	
	if($debug > 1)
		echo "\noffset_time: " . $offset_time . " mins \ntrain_data_time: " . date('Y-m-d H:i:s', strtotime($train_data_time)). "\ntrain_start_time: ". $train_start_time. PHP_EOL;
	
	if($connection["is_via"] == 1)
	{
		
		$train_table_via = $connection["via"]["train_table"];
		if($connection["flip"] == 1)
			$sql = "SELECT id, station_id, starts, price, path, down FROM ". $train_table." WHERE starts='". $connection["via_connection"]."';";
		else
			$sql = "SELECT id, station_id, starts, price, path, up FROM ". $train_table." WHERE starts='". $connection["via_connection"]."';";
	
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			if($connection["flip"] == 1)
				$end_time_offset = $row["down"];
			else
				$end_time_offset = $row["up"];
		}
		
		if($connection["flip"] == 1)
			$sql = "SELECT id, station_id, starts, price, path, up FROM ". $connection["via"]["train_table"]." WHERE starts='". $to_station["station"]."';";
		else
			$sql = "SELECT id, station_id, starts, price, path, down FROM ". $connection["via"]["train_table"]." WHERE starts='". $to_station["station"]."';";

		$end_time_via_offset = 0;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			if($connection["flip"] == 1)
				$end_time_via_offset = $row["up"];
			else
				$end_time_via_offset = $row["down"];
		}
		if($debug > 1)
		{
			echo "\nend_time_offset: " . $end_time_offset ." mins\n";
			echo "\nend_time_via_offset: " . $end_time_via_offset ." mins\n";
		}
		
		$sql = "SELECT id, train_id, train_time FROM timetable WHERE train_id IN(SELECT train_id FROM train_list WHERE reverse=".$connection["flip"]." AND train_table='".$train_table."') AND train_time >='".$train_start_time."' ORDER BY train_time LIMIT 4;";
		$result = mysqli_query($conn, $sql);
		//echo $sql .PHP_EOL;
		$i=0;
		if($size = mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{
				$sql = "SELECT train_id, type, seat FROM train_list WHERE train_id=".$row["train_id"].";";
				$in_result = mysqli_query($conn, $sql);
				if($size = mysqli_num_rows($in_result) == 1)
				{
					$in_row = mysqli_fetch_assoc($in_result);
					$connection["train_list_count"] = $i + 1;
					$connection["train_list"][strval($i)]["count_no"] = $i;
					$connection["train_list"][strval($i)]["train_time_id"] = $row["id"];
					$connection["train_list"][strval($i)]["train_id"] = $row["train_id"];
					$connection["train_list"][strval($i)]["train_start_time"] = date('H:i:s', strtotime($row["train_time"]. ' +'. $offset_time.' minutes'));
					$connection["train_list"][strval($i)]["train_end_time"] = date('H:i:s', strtotime($row["train_time"]. ' +'. $end_time_offset.' minutes'));
					$connection["train_list"][strval($i)]["train_type"] = $in_row["type"];
				}
				
				
				$train_start_time_via = date('H:i:s', strtotime($connection["train_list"][strval($i)]["train_end_time"]. ' +15 minutes'));
				
				//echo var_dump($train_start_time_via) . PHP_EOL;
				$sql = "SELECT id, train_id, train_time FROM timetable WHERE train_id IN(SELECT train_id FROM train_list WHERE reverse=0 AND train_table='".$train_table_via."') AND train_time >='".$train_start_time_via."' ORDER BY train_time LIMIT 1;";
				//echo $sql .PHP_EOL;
				$in_in_result = mysqli_query($conn, $sql);
				$j=0;
				if($size = mysqli_num_rows($in_in_result) > 0)
				{
					while($row = mysqli_fetch_assoc($in_in_result))
					{
						$sql = "SELECT train_id, type, seat FROM train_list WHERE train_id=".$row["train_id"].";";
						//echo $sql .PHP_EOL;
						$in_in2_result = mysqli_query($conn, $sql);
						if(($size2 = mysqli_num_rows($in_in2_result)) == 1)
						{
							$in_in2_row = mysqli_fetch_assoc($in_in2_result);
							$connection["via"]["train_list_count"] = $connection["train_list_count"];
							$connection["via"]["train_list"][strval($i)]["count_no"] = $i + 1;
							$connection["via"]["train_list"][strval($i)]["train_time_id"] = $row["id"];
							$connection["via"]["train_list"][strval($i)]["train_id"] = $row["train_id"];
							$connection["via"]["train_list"][strval($i)]["train_start_time"] = date('H:i:s', strtotime($row["train_time"]. ' +0 minutes'));
							$connection["via"]["train_list"][strval($i)]["train_end_time"] = date('H:i:s', strtotime($row["train_time"]. ' +'. $end_time_via_offset.' minutes'));
							$connection["via"]["train_list"][strval($i)]["train_type"] = $in_in2_row["type"];
						}
						$j++;
					}
				}
				$i++;
			}
		}		
	}
	else
	{
		if($connection["flip"] == 1)
			$sql = "SELECT id, station_id, starts, price, path, down FROM ". $train_table." WHERE starts='". $to_station["station"]."';";
		else
			$sql = "SELECT id, station_id, starts, price, path, up FROM ". $train_table." WHERE starts='". $to_station["station"]."';";
		
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			if($connection["flip"] == 1)
				$end_time_offset = $row["down"];
			else
				$end_time_offset = $row["up"];
		}
		
		if($debug > 1)
			echo "\nend_time_offset: " . $end_time_offset ." mins\n";
		
		$sql = "SELECT id, train_id, train_time FROM timetable WHERE train_id IN(SELECT train_id FROM train_list WHERE reverse=".$connection["flip"]." AND train_table='".$train_table."') AND train_time >='".$train_start_time."' ORDER BY train_time LIMIT 4;";
		$result = mysqli_query($conn, $sql);
		//echo $sql .PHP_EOL;
		$i=0;
		if(($size = mysqli_num_rows($result)) > 0)
		{
			while($row = mysqli_fetch_assoc($result))
			{
				$sql = "SELECT train_id, type, seat FROM train_list WHERE train_id=".$row["train_id"].";";
				$in_result = mysqli_query($conn, $sql);
				if($size = mysqli_num_rows($in_result) == 1)
				{
					$in_row = mysqli_fetch_assoc($in_result);
					$connection["train_list_count"] = $i + 1;
					$connection["train_list"][strval($i)]["count_no"] = $i;
					$connection["train_list"][strval($i)]["train_time_id"] = $row["id"];
					$connection["train_list"][strval($i)]["train_id"] = $row["train_id"];
					$connection["train_list"][strval($i)]["train_start_time"] = date('H:i:s', strtotime($row["train_time"]. ' +'. $offset_time.' minutes'));
					$connection["train_list"][strval($i)]["train_end_time"] = date('H:i:s', strtotime($row["train_time"]. ' +'. $end_time_offset.' minutes'));
					$connection["train_list"][strval($i)]["train_type"] = $in_row["type"];
				}
				$i++;
			}
		}
		
		//echo $sql .PHP_EOL;
	}
	
	return $connection;
	
}

function get_additional_cost($class, $no_of_passenger,$connection)
{
	global $conn, $env, $debug;
	$days = 0;
	$multiplier = 0;
	$sql = "SELECT type, multiplier FROM class WHERE type=".$class.";";
	$result = mysqli_query($conn, $sql);
	if(mysqli_num_rows($result) == 1)
	{
		$row = mysqli_fetch_assoc($result);
		$multiplier  = $row["multiplier"];
	}
	
	$connection["price"] = $connection["price"] * $multiplier;
	if($connection["is_via"] == 1)
		$connection["via"]["price"] = $connection["via"]["price"] * $multiplier;
	
	$date = strtotime($connection["requested_date_time"]);
	$from = date('Y-m-d H:i:s', $date);
	$today = date("Y-m-d H:i:s");
	if($env == "prod")
		$sql = "SELECT DATEDIFF('day, ".$from."', '".$today."') AS DateDiff;";
	else
		$sql = "SELECT DATEDIFF('".$from."', '".$today."') AS DateDiff;";
	$result = mysqli_query($conn, $sql);
	if(($size = mysqli_num_rows($result)) > 0)
	{
		$row = mysqli_fetch_assoc($result);
		$days  = $row["DateDiff"];
	}
	if($debug > 1)
	{
		echo $sql . PHP_EOL;
		echo "requested DT: " . $date . PHP_EOL;
		echo "today DT: " . $from . PHP_EOL;
		echo "difference days: " . abs($days) . PHP_EOL;
	}
	
	$sql = "SELECT days, multiplier FROM days_left WHERE days >=".abs($days)." LIMIT 1;";
	$result = mysqli_query($conn, $sql);
	if(($size = mysqli_num_rows($result)) > 0)
	{
		$row = mysqli_fetch_assoc($result);
		$multiplier  = $row["multiplier"];
	}
	
	$connection["price"] = $connection["price"] * $multiplier;
	if($connection["is_via"] == 1)
	{
		$connection["via"]["price"] = $connection["via"]["price"] * $multiplier;
		$connection["total_price"] = ($connection["price"] + $connection["via"]["price"] ) * $no_of_passenger;
	}
	else
		$connection["total_price"] = ($connection["price"]) * $no_of_passenger;
	
	
	if($debug > 1)
	{
		echo $sql . PHP_EOL;
		echo "\nget_additional_cost\n\tprice: " .$connection["price"]. "\n\ttotal_price: " . $connection["total_price"]. PHP_EOL;
	}
		
	return $connection;
}


function post_search($decoded_post,$uuid)
{
	/* decode post json OR input in post method
	uuid
	train_time_id
	from_station_id
	to_station_id
	train_start_time
	train_end_time
	train_date
	class
	booking_time = current_timestamp()
	connecting
	connecting_station_id
	connecting_train_time_id
	connecting_train_start_time
	connecting_train_end_time
	payment_status
	amount
	no_of_passenger
	special_service_flag
	seats
	food_service
	luggage_service
	*/
	
	/* output params
	status
	uuid
	payment_status
	special_service_flag
	seat_no
	via_seat_no
	*/
	
	global $conn, $debug;
	
	$seat_no = "";
	$return_post = array();
	$return_post["uuid"] = $uuid;
	$return_post["status"] = "complete";
	
	$train_date = date("Y-m-d",strtotime($decoded_post["train_date"]));
	$seats_occupied = 0;
	//echo $train_date . PHP_EOL;
	if($decoded_post["connecting"] == 0)
	{
		$seat_no = get_seats($decoded_post["train_time_id"], $decoded_post["no_of_passenger"], $train_date);
	}
	else
	{
		$seat_no = get_seats($decoded_post["train_time_id"], $decoded_post["no_of_passenger"], $train_date);
		if($seat_no != "")
			$via_seat_no = get_seats($decoded_post["connecting_train_time_id"], $decoded_post["no_of_passenger"], $train_date);
	}
	
	if($debug > 2)
	{
		echo "seat_no: " . $seat_no. PHP_EOL;
		if($decoded_post["connecting"] == 1)
			echo "via_seat_no: " . $via_seat_no. PHP_EOL;
	}
	
	$return_post["payment_status"] = $decoded_post["payment_status"];
	$return_post["special_service_flag"] = $decoded_post["special_service_flag"];
		
	
	if($seat_no == "")
	{
		$return_post["seat_no"]  = "0";
		$return_post["error"]  = "No seat available";
	}
	else
	{
		if($decoded_post["seats"] == 0)
		{
			$return_post["seat_no"] = "0";
			if($decoded_post["connecting"] == 1)
				$return_post["via_seat_no"] = "0";
		}
		else
		{
			$return_post["seat_no"] = $seat_no;
			if($decoded_post["connecting"] == 1)
				$return_post["via_seat_no"] = $via_seat_no;
		}
	}
	
	/* DB Query
	INSERT INTO `reservation` (`id`, `uuid`, `train_time_id`, `from_station`, `to_station`, `train_date`, `train_start_time`, `train_end_time`, `class`, `booking_time`, `connecting`, `connection_station_id`, `connecting_train_time_id`, `connecting_train_start_time`, `connecting_train_end_time`, `payment_status`, `amount`, `no_of_passenger`, `special_service_flag`, `seats`, `seat_no`, `connecting_seat_no`, `food_service`, `luggage_service`) VALUES (NULL, 'ab123', '99', '11', '1', '2022-06-30', '14:00:00', '15:00:00', '2', current_timestamp(), '0', NULL, NULL, NULL, NULL, 'INCOMPLETE', '105.00', '2', '1', '2', '1,2', NULL, '1', '1');
	*/
	if($seat_no != "")
	{
		
		$special_service_flag = 0;
		$luggage_service = 0;
		$food_service = 0;
		
		if((bool)$decoded_post["special_service_flag"] == true)
			$special_service_flag = 1;
		
		if((bool)$decoded_post["luggage_service"] == true)
			$luggage_service = 1;
		
		if((bool)$decoded_post["food_service"] == true)
			$food_service = 1;
		
		
		if($decoded_post["connecting"] == 0)
			$sql = "INSERT INTO reservation (id, uuid, train_time_id, from_station, to_station, train_date, train_start_time, train_end_time, class, booking_time, connecting, connecting_station_id, connecting_train_time_id, connecting_train_start_time, connecting_train_end_time, payment_status, amount, no_of_passenger, special_service_flag, seats, seat_no, connecting_seat_no, food_service, luggage_service ) VALUES (NULL, '".$uuid."', '".$decoded_post["train_time_id"]."', '".$decoded_post["from_station_id"]."', '".$decoded_post["to_station_id"]."', '".$train_date."', '".$decoded_post["train_start_time"]."', '".$decoded_post["train_end_time"]."', '".$decoded_post["class"]."', current_timestamp(), '".$decoded_post["connecting"]."', NULL, NULL, NULL, NULL, '".$return_post["payment_status"]."', '".$decoded_post["amount"]."', '".$decoded_post["no_of_passenger"]."', '".$special_service_flag."', '".$decoded_post["seats"]."', '".$seat_no."', NULL, '".$food_service."', '".$luggage_service."');";
		else
			$sql = "INSERT INTO reservation (id, uuid, train_time_id, from_station, to_station, train_date, train_start_time, train_end_time, class, booking_time, connecting, connecting_station_id, connecting_train_time_id, connecting_train_start_time, connecting_train_end_time, payment_status, amount, no_of_passenger, special_service_flag, seats, seat_no, connecting_seat_no, food_service, luggage_service ) VALUES (NULL, '".$uuid."', '".$decoded_post["train_time_id"]."', '".$decoded_post["from_station_id"]."', '".$decoded_post["to_station_id"]."', '".$train_date."', '".$decoded_post["train_start_time"]."', '".$decoded_post["train_end_time"]."', '".$decoded_post["class"]."', current_timestamp(), '".$decoded_post["connecting"]."', '".$decoded_post["connecting_station_id"]."', '".$decoded_post["connecting_train_time_id"]."', '".$decoded_post["connecting_train_start_time"]."', '".$decoded_post["connecting_train_end_time"]."', '".$return_post["payment_status"]."', '".$decoded_post["amount"]."', '".$decoded_post["no_of_passenger"]."', '".$special_service_flag."', '".$decoded_post["seats"]."', '".$seat_no."', '".$via_seat_no."', '".$food_service."', '".$luggage_service."');";
		
		//echo "SQL: " .$sql . PHP_EOL;
		
		if (mysqli_query($conn, $sql)) 
		{
		
		}
		else 
		{
			header("HTTP/1.1 500 Internal Server Error");
			exit();
		}
	}
	
	echo json_encode($return_post);
	
	return true;
}

function get_seats($train_time_id, $no_of_passenger, $train_date)
{
	global $conn, $debug;
	
	$seat_no = "";
	$seats_occupied = 0;
	
	$sql = "SELECT SUM(seats) AS seats_occupied FROM reservation WHERE train_time_id=".$train_time_id." AND train_date='".$train_date."';";
	$result = mysqli_query($conn, $sql);
	$size = mysqli_num_rows($result);
	if($size > 0)
	{
		$row = mysqli_fetch_assoc($result);
		$seats_occupied = (int)$row["seats_occupied"];
	}
	else
	{
		$seats_occupied = 0;
	}
	
	$sql = "SELECT seat FROM train_list WHERE train_id IN(SELECT train_id FROM timetable WHERE id=".$train_time_id.");";
	$result = mysqli_query($conn, $sql);
	$size = mysqli_num_rows($result);
	if($size > 0)
	{
		$row = mysqli_fetch_assoc($result);
		$max_seats = (int)$row["seat"];
	}
	
	$seat_remaining = $max_seats - $seats_occupied;
	
	if($seat_remaining < $no_of_passenger)
	{
		$seat_no = "";
	}
	else
	{
		$sql = "SELECT SUM(seats) AS total_seats_occupied FROM reservation WHERE ( train_time_id=".$train_time_id." AND train_date='".$train_date."' ) OR ( connecting_train_time_id=".$train_time_id." AND train_date='".$train_date."' ) ORDER BY booking_time DESC LIMIT 1;";
		//echo "sql: " . $sql. PHP_EOL;
		$result = mysqli_query($conn, $sql);
		$size = mysqli_num_rows($result);
		$last_seat_no = 0;
		if($size > 0)
		{
			$row = mysqli_fetch_assoc($result);
			$last_seat_no = (int)$row["total_seats_occupied"];
			
			//echo "last seat no: " . $last_seat_no . PHP_EOL;
			//echo "no_of_passenger: " . $decoded_post["no_of_passenger"] . PHP_EOL;
			
			$seat_no = $last_seat_no +1;
			$i = 1;
			
			for($i = 1; $i < $no_of_passenger; $i++)
			{
				$seat_no = sprintf('%1$s,%2$s', $seat_no, ((int)$seat_no + (int)$i));
			}
		}
		else
		{
			$i = 1;
			$seat_no = "1";
			for($i = 2; $i <= $no_of_passenger; $i++)
			{
				$seat_no = sprintf('%1$s,%2$s', $seat_no, $i);
			}
		}
		
		//echo "seat_no: " . $seat_no. PHP_EOL;
	}
	//echo "RE: " .$seat_remaining . PHP_EOL;
	
	return $seat_no;
}

function put_search($decoded_put,$uuid)
{
	/* input
	uuid
	payment_status
	special_service_flag
	seat_no
	food_service
	luggage_service
	*/
	
	/* output
	status
	uuid
	seat_no
	*/
	global $conn, $debug;
	
	$special_service_flag = 0;
	$luggage_service = 0;
	$food_service = 0;
	
	if((bool)$decoded_put["special_service_flag"] == true)
		$special_service_flag = 1;
	
	if((bool)$decoded_put["luggage_service"] == true)
		$luggage_service = 1;
	
	if((bool)$decoded_put["food_service"] == true)
		$food_service = 1;
	
	$return_post["uuid"] = $uuid;
	$return_post["status"] = "complete";
	
	/*
	UPDATE reservation 
	SET special_service_flag = '1' , luggage_service = '1' , food_service = '1'
	WHERE uuid = abc123;
	*/
	$sql = "UPDATE reservation SET payment_status = '".$decoded_put["payment_status"]."', special_service_flag = '".$special_service_flag."' , luggage_service = '".$luggage_service."' , food_service = '".$food_service."' WHERE uuid = '".$uuid."';";
	
	
	//echo "SQL: " .$sql. PHP_EOL;
	
	if (mysqli_query($conn, $sql)) 
	{
		$return_post["status"] = "complete";
	}
	else 
	{
		header("HTTP/1.1 500 Internal Server Error");
		exit();
	}
	
	
	if($decoded_put["seat_no"] > 0)
	{
		$sql = "SELECT seat_no FROM reservation WHERE uuid='".$uuid."' ORDER BY booking_time DESC LIMIT 1;";
		//echo "sql: " . $sql. PHP_EOL;
		$result = mysqli_query($conn, $sql);
		$size = mysqli_num_rows($result);
		if($size > 0)
		{
			$row = mysqli_fetch_assoc($result);
			$return_post["seat_no"] = $row["seat_no"];
		}
		else 
		{
			$return_post["status"] = "error";
			echo json_encode($return_post);
			header("HTTP/1.1 500 Internal Server Error");
			exit();
		}
		
	}
	
	echo json_encode($return_post);
	return true;
	
}

function delete_search($uuid)
{
	/* input
	uuid
	*/
	
	/* output
	status
	uuid
	*/
	
	global $conn, $debug;
	$return_post["uuid"] = $uuid;
	$return_post["status"] = "complete";
	
	$sql = "DELETE FROM reservation WHERE uuid = '".$uuid."';";
	
	
	//echo "SQL: " .$sql. PHP_EOL;
	
	if (mysqli_query($conn, $sql)) 
	{
		$return_post["status"] = "complete";
	}
	else 
	{
		header("HTTP/1.1 500 Internal Server Error");
		exit();
	}
	
	echo json_encode($return_post);
	return true;
}

function get_enquire($uuid)
{
	global $conn, $debug;
	
	$return_enquire = array();
		
	$return_enquire["uuid"] = $uuid;
	$return_enquire["status"] = "complete";
	
	
	$sql = "SELECT uuid, train_time_id, from_station, to_station, train_date, train_start_time, train_end_time, class, booking_time, connecting, connecting_station_id, connecting_train_time_id, connecting_train_start_time, connecting_train_end_time, payment_status, amount, no_of_passenger, special_service_flag, seats, seat_no, connecting_seat_no, food_service, luggage_service FROM reservation WHERE uuid='".$uuid."' ORDER BY booking_time DESC LIMIT 1;";
	
	//echo "sql: " . $sql. PHP_EOL;
	
	$result = mysqli_query($conn, $sql);
	$size = mysqli_num_rows($result);
	
	if($size > 0)
	{
		$row = mysqli_fetch_assoc($result);
		$return_enquire["train_time_id"] = $row["train_time_id"];
		$return_enquire["from_station_id"] = $row["from_station"];
		$return_enquire["to_station_id"] = $row["to_station"];
		$return_enquire["train_date"] = $row["train_date"];
		$return_enquire["train_start_time"] = $row["train_start_time"];
		$return_enquire["train_end_time"] = $row["train_end_time"];
		$return_enquire["class"] = $row["class"];
		$return_enquire["booking_time"] = $row["booking_time"];
		$return_enquire["connecting"] = $row["connecting"];
		if($row["connecting"] == 1)
		{
			$return_enquire["connecting_station_id"] = $row["connecting_station_id"];
			$return_enquire["connecting_train_time_id"] = $row["connecting_train_time_id"];
			$return_enquire["connecting_train_start_time"] = $row["connecting_train_start_time"];
			$return_enquire["connecting_train_end_time"] = $row["connecting_train_end_time"];
		}
		$return_enquire["payment_status"] = $row["payment_status"];
		$return_enquire["amount"] = $row["amount"];
		$return_enquire["no_of_passenger"] = $row["no_of_passenger"];
				
		$return_enquire["special_service_flag"] = false;
		$return_enquire["seats"] = $row["seats"];
		if($row["seats"] > 0)
		{
			$return_enquire["seat_no"] = $row["seat_no"];
			if($row["connecting"] == 1)
			$return_enquire["connecting_seat_no"] = $row["connecting_seat_no"];
		}
		$return_enquire["food_service"] = false;
		$return_enquire["luggage_service"] = false;
		
		if($row["special_service_flag"] == 1)
			$return_enquire["special_service_flag"] = true;
		if($row["food_service"] == 1)
			$return_enquire["food_service"] = true;
		if($row["luggage_service"] == 1)
			$return_enquire["luggage_service"] = true;
	}
	else
	{
		$return_enquire["status"] = "error";
		echo json_encode($return_enquire);
		header("HTTP/1.1 500 Internal Server Error");
		exit();
	}
	
	echo json_encode($return_enquire);
	return true;
}

?>