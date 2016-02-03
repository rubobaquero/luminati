Luminati PHP SDK
================

Library for making HTTP / HTTPS concurrent requests using curl_multi via the [Luminati Service](https://luminati.io/).

You will need at least a free [Luminati Trial Account](http://luminati.io/?affiliate=email/ruben@pincelpixel.com).

Installation
------------

Install using composer:

```bash
composer require rubobaquero/luminati
```

Usage
-----

Instance a Luminati object with username, password and optional zone (gen by default):

```php
$luminati = new Luminati($username,$password,"gen");
```

Prepare an array with a few requests. Each request is defined by an array with the following keys:
- `url` (mandatory): URL of the request.
- `callback` (mandatory): Callback function that will be called with the result of the CURL request.
- `options` (optional): [CURL options](http://php.net/manual/es/function.curl-setopt.php) of the request.
- `user_data` (optional): Info that you will want to pass as parameter to the callback function
- `country` (optional): ISO Country code of the request.
- `session` (optional): Luminati Session. If you want to make the requests using the same exit node yoy can specify one. If you don´t set any, a random one is generated.

Example:
```php
$urls = array();
for($i=0;$i<10;$i++){
	$urls[] = array(
		'url' => 'https://www.wikipedia.org',
		'options' => array(
			CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36"
		),
		'callback' => 'callback_func',
		'user_data' => array(
			'some' => 'useful data'
		),
		'country' => 'es'
 	);
}
```

Then, write a callback function that accepts the following parameters:
- `response`: Body of the response
- `url`: URL of the request
- `request_info`: Information of the request given by [curl_getinfo()](http://php.net/manual/es/function.curl-getinfo.php)
- `user_data`: User data of the request
- `time`: Execution time of the request

Example:
```php
function callback_func($response, $url, $request_info, $user_data, $time){
	echo "We have a response from $url";
}
```

Finally, make 5 concurrent requests with a timeout of 20 seconds each one:
```php
$luminati->make_requests($urls,5,20);
```