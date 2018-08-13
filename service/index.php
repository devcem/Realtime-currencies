<?php

	header('Content-Type: application/json');

	include 'components/Currency.php';

	$output     = array();
	$cache_dir  = './cache/';

	//Change currency values with this value
	$currencies = array('usd-try', 'eur-try');

	foreach ($currencies as $key => $currency_name) {
		$currency = new Currency($currency_name, $cache_dir);
	
		$output[$currency_name] = array(
			'value' => $currency->getValue(),
			'direction' => $currency->getDirection()
		);
	}

	echo json_encode($output);