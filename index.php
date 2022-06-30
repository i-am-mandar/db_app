<?php
require_once "config.php";
require_once "app_driver.php";
require_once "db_helper.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$uuid = "";
$requestMethod = $_SERVER["REQUEST_METHOD"];

//echo var_dump($uri);
if($env == "prod")
{
	if ($uri[2] !== 'train') 
	{
        header("HTTP/1.1 404 Not Found");
        exit();
    }
    
    if (isset($uri[3])) 
	{
        $operation = $uri[3];
		if($operation === "booking")
		{
			if(($requestMethod == "POST") || ($requestMethod == "PUT") || ($requestMethod == "DELETE"))
			{
				if(isset($uri[4]))
					$uuid = $uri[4];
				else
				{
					header("HTTP/1.1 404 Not Found");
					exit();
				}
			}
		}
		else if($operation === "enquire")
		{
			if($requestMethod == "GET")
			{
				if(isset($uri[4]))
					$uuid = $uri[4];
				else
				{
					header("HTTP/1.1 404 Not Found");
					exit();
				}
			}
		}
    }
    else
    {
    	header("HTTP/1.1 404 Not Found");
        exit();
    }

}
else if($env == "dev")
{
	if ($uri[3] !== 'train') 
	{
        header("HTTP/1.1 404 Not Found");
        exit();
    }
    
    if (isset($uri[4])) 
	{
        $operation = $uri[4];
		if($operation === "booking")
		{
			if(($requestMethod == "POST") || ($requestMethod == "PUT") || ($requestMethod == "DELETE"))
			{
				if(isset($uri[5]))
					$uuid = $uri[5];
				else
				{
					header("HTTP/1.1 404 Not Found");
					exit();
				}
			}
		}
		else if($operation === "enquire")
		{
			if($requestMethod == "GET")
			{
				if(isset($uri[5]))
					$uuid = $uri[5];
				else
				{
					header("HTTP/1.1 404 Not Found");
					exit();
				}
			}
		}
    }
    else
    {
    	header("HTTP/1.1 404 Not Found");
        exit();
    }

}
else
{
	header("HTTP/1.1 404 Not Found");
    exit();
}
	
processRequest($requestMethod, $operation, $uuid);

?>