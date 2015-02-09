<?php

class extClima {
	var $city;
	var $xml;
	public function __construct() {
		global $zihweb, $cls_cfg, $cls_cnt;
		if (isset($cls_cfg->gcfg['geo-city'])) {
			$city = $cls_cfg->gcfg['geo-city'];
		} else {
			$city = 'Cozumel';
		}
		//$xmlurl = ZW_URL.'feedproxy/http://www.google.com/ig/api?weather='.$city;
		$xmlurl = ZW_URL.'feedproxy/http://weather.yahooapis.com/forecastrss?w='.$city;
		$agent = $cls_cfg->agent; //"Opera/9.80 (Windows NT 6.1; U; ".$cfg['idi']."-LA) Presto/2.10.229 Version/11.60";

		//$zihweb = new ZihWebCMS();
		$this->xml = $zihweb->curly($xmlurl);
		/*
		 $xml = curl_init();
		curl_setopt($xml, CURLOPT_URL, $xmlurl );
		curl_setopt($xml, CURLOPT_HEADER, false);
		curl_setopt($xml, CURLOPT_USERAGENT, $agent);
		curl_setopt($xml, CURLOPT_HTTPHEADER, array('Accept-Language: '.$cfg['idi']));
		curl_setopt($xml, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($xml, CURLOPT_VERBOSE, false);
		curl_setopt($xml, CURLOPT_TIMEOUT, 5);
		*/
		/*
		 curl_setopt($xml, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($xml, CURLOPT_SSLVERSION,3);
		curl_setopt($xml, CURLOPT_SSL_VERIFYHOST, FALSE);
		*/
		//$xml = curl_exec($xml);
		$this->xml = simplexml_load_string($this->xml);
		//curl_close($xml);
		$information = $this->xml->xpath("/xml_api_reply/weather/forecast_information");
		$current = $this->xml->xpath("/xml_api_reply/weather/current_conditions");
		$this->forecast_list = $this->xml->xpath("/xml_api_reply/weather/forecast_conditions");
		$citydata = $information[0]->city['data'];
		//$icondata = str_replace(".gif", ".png", $current[0]->icon['data']);
		$temp = $current[0]->temp_c['data'];
		$condition = $current[0]->condition['data'];
	}
	
	public function &extcnt() {
		global $cls_cnt;
		$weatheron = $cls_cnt->remTxt('[es]Clima en[/es][en]Weather on[/en]');
		$forecasts = $cls_cnt->remTxt('[es]Pronóstico[/es][en]Forecast[/en]');
		$scale = $cls_cnt->remTxt('[es]C[/es][en]F[/en]');
		foreach ($this->forecast_list as $forecast) {
			$icod = $forecast->icon['data'];
			$icod = str_replace(".gif", ".png", $icod);
			$day = $forecast->day_of_week['data'];
			$low = $forecast->low['data'];
			$high = $forecast->high['data'];
			$cond = $forecast->condition['data'];
			$forecastf .= <<< EOPAGE
        <div class="weather">
            <img src="http://www.google.com$icod" alt="weather">
            <div>$day</div>
            <span class="cond">
	            $low&deg; $scale - $high&deg; $scale,
	            $cond
            </span>
        </div>
EOPAGE;
		}
		$extcnt = <<< EOPAGE
        <h1>$weatheron $citydata</h1>
        <div class="weather">
            <img src="http://www.google.com$icondata" alt="weather">
            <span class="cond">
            $temp &deg; $scale,
            $condition
            </span>
        </div>
        <h2>$forecasts</h2>
        $forecastf
EOPAGE;
		return $extcnt;
	}
}
