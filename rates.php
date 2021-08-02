<?php
define('RATES_URL',	'http://data.fixer.io/api/latest?access_key=3c3fb73533c621743babaeeae044bd1d');

@date_default_timezone_set("GMT");



if (file_exists('rates.xml')) {
    copy('rates.xml', 'rates'.'_'.time().'.xml');
    $xml = simplexml_load_file('rates.xml');
    foreach($xml->rate as $r) {
        if ((string) $r['live'] == '1') {
            $live[] = (string) $r['code'];
        }
    }
}

#decode the json to a php array
$pulled_rates = json_decode($json_rates, true);

#declare our empty rates array
$rates = array();

#get the timestamp into the rates array
$rates['ts'] = $pulled_rates['timestamp'];

#set the GBP rate to 1 and put into rates array
$rates['GBP'] = '1.000000';

#work out the conversion rate
$eur_rate = $pulled_rates['rates']['GBP'];

#build the rates array with rate converted (to £) 
#rounded to 6 decimal places
foreach ($pulled_rates['rates'] as $curr => $amnt) {
   if ($curr != 'GBP') {
     $gbp_rate = (1/$eur_rate) * $amnt;
	 $rates[$curr] = round($gbp_rate, 6);
   }
}

#printing the array to view
echo '<pre>'.print_r($rates, true).'</pre>';

else {
# array of the ISO 4217 currencies
    $live = array(
        'AUD', 'BRL', 'CAD','CHF',
        'CNY', 'DKK', 'EUR','GBP',
        'HKD', 'HUF', 'INR','JPY',
        'MXN', 'MYR', 'NOK','NZD',
        'PHP', 'RUB', 'SEK','SGD',
        'THB', 'TRY', 'USD','ZAR'
    );
}

# cacculation using GBP as base currency for conversion
$gbp_rate = 1/$rates->rates->GBP;

# start and initialize the writer
$writer = new XMLWriter();
$writer->openURI('rates.xml');
$writer->startDocument("1.0", "UTF-8");
$writer->startElement("rates");
$writer->writeAttribute('base', 'GBP');
$writer->writeAttribute('ts', $rates->timestamp);
foreach ($rates->rates as $code=>$rate) {
	$writer->startElement("rate");
    $writer->writeAttribute('code', $code);

    if ($code=='GBP') {
        $writer->writeAttribute('rate', '1.00');
    }
    else {
        $writer->writeAttribute('rate', $rate * $gbp_rate);
    }

    if (in_array($code, $live)) {
        $writer->writeAttribute('live', '1');
    }
    else {
        $writer->writeAttribute('live', '0');
    }
    $writer->endElement();
}
$writer->endElement();
$writer->endDocument();
$writer->flush();
exit;
?>
