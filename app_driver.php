<?php

function processRequest($requestMethod, $operation, $uuid)
{
	global $conn;
    switch ($requestMethod) {
        case 'GET':
            $response["method"] = "GET Method used";
			$get = array();
			parse_str($_SERVER['QUERY_STRING'], $get);
			if($operation === "booking")
				get_search($get);
			else if($operation === "enquire")
				get_enquire($uuid);
            break;
        case 'POST':
            $response["method"]  = "POST Method used";
			$post = file_get_contents('php://input');
			$decoded_post = json_decode($post, true);
			post_search($decoded_post, $uuid);
            break;
        case 'PUT':
            $response["method"]  = "PUT Method used";
			$put = file_get_contents('php://input');
			$decoded_put = json_decode($put, true);
			put_search($decoded_put, $uuid);
            break;
        case 'DELETE':
            $response["method"] = "DELETE Method used";
			delete_search($uuid);
            break;
        default:
            $response["method"] = "Bad Method";
			notFoundResponse();
            break;
    }
	$return_response = json_encode($response);
	//echo $return_response;
	mysqli_close($conn);
    return true;
	
}

function notFoundResponse()
{
    header("HTTP/1.1 404 Not Found");
    return false;
}


?>