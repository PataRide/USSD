<?php
include("login_test.php");
//capture GET variables from USSD call
$cell_number = $_GET['MSISDN'];
$session_id = $_GET['SESSION_ID'];
$service_code = $_GET['SERVICE_ID'];
$ussd_string = $_GET['USSD_STRING'];

//set default level to zero
 $level = 0;
$ussd_string = str_replace("#","*",$ussd_string);
$ussd_string_exploded = explode("*",$ussd_string);
$ussd_string_exploded2 = array_shift($ussd_string_exploded);

//get level id from ussd_string reply
$level = count($ussd_string_exploded);

$params = array('params'=>array('cell_number'=>$cell_number,
				'session_id'=>$session_id,
				'ussd_string'=>$ussd_string));

$phone = substr($cell_number,3,strlen($cell_number));

if($level == 0)
{
	display_menu();
}

if($level > 0)
{
	switch($ussd_string_exploded[0])
	{
		case 1:
			have($ussd_string_exploded,$phone);
		break;
		
		case 2:
			need($ussd_string_exploded,$phone);
		break;

		case 3:
			help($ussd_string_exploded,$phone);
		break;

	}
}

//Main menu USSD Reply	
function display_menu()
{
	$ussd_text = "Welcome to i-Match. Please select the menu options below and press call\n1. Have\n2. Need";
	ussd_proceed($ussd_text);
}

function have($details,$phone)
{
	$c = $details[1];
	if(count($details) == 1)
	{
		$categories = list_categories();
		$categories['desc'] = unserialize($categories['desc']);
		if($categories['code'] == 201)
		{	
		 $category = $categories['desc']['name'];

		//$ussd_text = "I have\n1. Food\n2. Blankets\n3. Water\n4. Clothes\n5. Grains\n6. Medicine\n7. Exit\n Reply options 1 - 7";
		$ussd_text = $category;
 		ussd_proceed($ussd_text);
		}
		else
		{
		ussd_proceed('No category');
		}
	}
	if(count($details) == 2)
	{
		$ussd_text = "Enter Quantity";
		ussd_proceed($ussd_text);

	}
	if(count($details) == 3)
	{
		/*	
		$params = array('params'=>'phone'=>$phone));
		$registered = authorize($params);
		if($registered['code'] == 201)
		{
			$registered['desc'] = unserialize($registered['desc']);
			$name = $registered['desc']['name'];
			$createdby = $registered['desc']['id'];

		}
		*/
		$type = $details[0];
		$item = $details[1];
		$quantity = $details[2];
		$params = array('params'=>array('category'=>$item,'quantity'=>$quantity,'type'=>$type));
		$have = add($params);

		if($have['code'] == 201)
		{
			$ussd_text = "Thank you for your support";
			ussd_proceed($ussd_text);
		}
		else
		{
			$ussd_text = "Seems your have could not be added at this time, try again";
			ussd_stop($ussd_text);
		}
	}
}


function need($details,$phone)
{
	$c = $details[1];
	if(count($details) == 1)
	{
		$categories = list_categories();
		$categories['desc'] = unserialize($categories['desc']);
		if($categories['code'] == 201)
		{	
		 $category = $categories['desc']['name'];

		//$ussd_text = "I have\n1. Food\n2. Blankets\n3. Water\n4. Clothes\n5. Grains\n6. Medicine\n7. Exit\n Reply options 1 - 7";
		$ussd_text = $category;
 		ussd_proceed($ussd_text);
		}
	}
	if(count($details) == 2)
	{
		$ussd_text = "Enter Quantity";
		ussd_proceed($ussd_text);

	}
	if(count($details) == 3)
	{
		$type = $details[0];
		$item = $details[1];
		$quantity = $details[2];
		$params = array('params'=>array('category'=>$item,'quantity'=>$quantity,'type'=>$type));
		$need = add($params);

		if($need['code'] == 201)
		{
			$ussd_text = "Thank you for the request, will get back to you in no time";
			ussd_proceed($ussd_text);
		}
		else
		{
			$ussd_text = "Seems your need could not be added at this time, try again";
			ussd_stop($ussd_text);
		}
	}
}



function help($details,$phone)
{
	$c = $details[1];
	if(count($details) == 1)
	{
		$ussd_text = "Help\n1. You have to be registered to use this service\n2. Register on www.i-match.com\n3. Please contact us on 0719167696 for any queries.";
		ussd_proceed($ussd_text);
	}
}


//USSD proceed reply
function ussd_proceed($ussd_text)
{
	echo "CON $ussd_text";
	exit(0);
}

//USSD stop reply
function ussd_stop($ussd_text)
{
	echo "END $ussd_text";
	exit(0);
}
?>