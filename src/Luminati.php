<?php
 
namespace Rubobaquero;

use marcushat\RollingCurlX;

class Luminati {
	
	private $username;
	private $password;
	private $zone;
	
	const SUPERPROXY = 'http://zproxy.luminati.io:22225';
	const TIMEOUT = 60; 			// Default timeout in seconds
	const MAX_CONNECTIONS = 20;		// Default max connections
	
	public function __construct($username, $password, $zone = "gen")
	{
		
		$this->username = $username;
		$this->password = $password;
		$this->zone = $zone;
		
	}
	
	public function make_requests($urls, $max_connections = self::MAX_CONNECTIONS, $timeout = self::TIMEOUT)
	{
				
		$timeout = $timeout*1000;
		
		set_time_limit(0);

		$RCX = new RollingCurlX($max_connections);
		$RCX->setTimeout($timeout);
		
		// Add URLs with parameters
		foreach($urls as $url){
			isset($url['options']) ? $options = $url['options'] : $options = array();
			isset($url['user_data']) ? $user_data = $url['user_data'] : $user_data = NULL;
			isset($url['callback']) ? $callback = $url['callback'] : $callback = NULL;
			isset($url['country']) ? $country = $url['country'] : $country = NULL;
			isset($url['session']) ? $session = $url['session'] : $session = NULL;
			
			// Session and Country
			$username = $this->get_proxy_user_password();
			
			// Insert Super Proxy in Options
			$options[CURLOPT_PROXY] = self::SUPERPROXY;
			$options[CURLOPT_PROXYUSERPWD] = $this->get_proxy_user_password($country, $session);
			
			$RCX->addRequest($url['url'], NULL, $callback, $user_data, $options);
		}

		// Execute RCX
		$res = $RCX->execute();
		return $res;
	}
	
	private function get_proxy_user_password($country = NULL, $session = NULL)
	{
		$username = $this->username;
		
		// Add zone
		$username .= "-zone-".$this->zone;
		
		// Username including country
		if($country) $username .= "-country-".$this->country;
		
		// If not session defined, choose a random one
		if(!$session) $session = "rand".rand(0,100000000);
		$username .= "-session-".$session;
		
		return $username.":".$this->password;
	}	
}