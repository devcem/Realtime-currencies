<?php

	class Currency
	{
		
		private $service = 'https://tr.investing.com/currencies/';
		private $html    = '';
		private $xpath   = '';

		function __construct($currency, $cache_dir){
			$cache = $cache_dir . '/' . md5($currency);

			if(file_exists($cache) && time() - filemtime($cache) < 1000){
				$this->html = file_get_contents($cache);
			}else{
				$this->html = $this->get($this->service . $currency);

				file_put_contents($cache, $this->html);
			}

			$html_dom = new DOMDocument();
			@$html_dom->loadHTML($this->html);
			$this->xpath = new DOMXPath($html_dom);
		}

		function get($url){
			$handle = curl_init($url);

		    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36');

		    $response = curl_exec($handle);
		    $info     = curl_getinfo($handle);

		    $this->status    = $info['http_code'];
		    $this->load_time = $info['total_time'];

		    curl_close($handle);

		    return $response;
		}

		function getValue(){
			$nodes = $this->xpath->query('//*[@id="last_last"]');

			return $nodes[0]->nodeValue;
		}

		function getDirection(){
			$nodes = $this->xpath->query('//*[@id="quotes_summary_current_data"]/div[1]/div[1]');

			return strpos($nodes[0]->getAttribute('class'), 'upArrow') > -1 ? 'up' : 'down';
		}
	}