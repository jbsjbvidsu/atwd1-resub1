<?php
@date_default_timezone_set("GMT");

# parammitters array
define('PARAMS', array('to', 'from', 'amnt', 'format'));

# this holds the error code and the display messsages for the error.
define ('ERROR_HASH', array(
	1000 => 'Required parameter is missing',
	1100 => 'Parameter not recognized',
	1200 => 'Currency type not recognized',
	1300 => 'Currency amount must be a decimal number',
	1400 => 'Format must be xml or json',
	1500 => 'Error in Service',
	2000 => 'Action not recognized or is missing',
	2100 => 'Currency code in wrong format or is missing',
	2200 => 'Currency code not found for update',
	2300 => 'No rate listed for currency',
	2400 => 'Cannot update base currency',
	2500 => 'Error in service'
));

# ensure Parammitter values match the keys in $GET
if (count(array_intersect(PARAMS, array_keys($_GET))) < 4) {
    echo generate_error(1000, $_GET['format']);
    exit();
}

# ensure no extra parammitters
if (count($_GET) > 4) {
	echo generate_error(1100, $_GET['format']);
	exit();
}

# this ensure that the currency code entered match recognised currencies
if (!in_array($_GET['to'], $codes) || !in_array($_GET['from'], $codes)) {
    echo generate_error(1200, $_GET['format']);
	exit;
}

# $amnt is enter as correct monetary value i.e. 6.34 not 6.345
if (!preg_match('/^\d+(\.\d{1,2})?$/', $_GET['amnt'])) {
	echo generate_error(1300, $_GET['format']);
	exit;
}

# check for allowed format values
if (!in_array( $_GET['format'], FRMTS)) {
	echo generate_error(1400);
	exit;
}

# if to and from are the same - set rate to 1.00
if ($_GET['from']==$_GET['to']) {
	$rate = 1.00;
	$conv =  $_GET['amnt'];
}
else {
	# convertion calculator
	$rate = floatval($tr) / floatval($fr);

	# calculate the conversion of one currency to another 
	$conv = $rate * $_GET['amnt'];
}

{
	$json = simplexml_load_string('<conv>'.$response.'</conv>');
	header('Content-Type: application/json');
	echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

}

?>
