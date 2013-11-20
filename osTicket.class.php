<?php

/**
 * Simple class to include to access the API of osTicket via PHP
 *
 * Usage:
 * $osTicket = new osTicket($api_key, $api_url);
 * $osTicket->CreateTicket(array("ticket_field" => "value"));
 *
 * On success, returns TRUE
 *
 * @author     Adam Link (rhyeal on github)
 * @link       	https://github.com/rhyeal/osTicket-php-class
 
 */
	
class osTicket
{
	const TICKET_SUCCESS = true;
	const TICKET_FAIL = false;
	
	private $_apiKey;
	private $_url;
	
	public function __construct($api_key = '', $api_url = '')
	{
		$this->_apiKey = $api_key;
		$this->_url = $api_url;
	}
	
	public function setApiKey($api_key = '')
	{
		$this->_apiKey = $api_key;
	}
	
	public function getApiKey()
	{
		return $this->_apiKey;
	}
	
	public function setApiUrl($api_url = '')
	{
		$this->_url = $api_url;
	}
	
	public function getApiUrl()
	{
		return $this->_url;
	}
	
	// See https://github.com/osTicket/osTicket-1.7/blob/develop/setup/doc/api/tickets.md for full list of variables and options that you can pass.
	public function CreateTicket($ticket_array = array())
	{
		function_exists('curl_version') or die('CURL support required');
		
		$data_string = json_encode($ticket_array);
		
		set_time_limit(30);
		
		// curl post
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$this->_apiKey));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		curl_close($ch);
		
		if(preg_match('/HTTP\/.* ([0-9]+) .*/', $result, $status) && $status[1] == 201)
		{
			return osTicket::TICKET_SUCCESS;
		}

		return osTicket::TICKET_FAIL;
	}
}

?>