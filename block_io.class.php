<?php

class BlockIO
{
	/* settings - do not touch them! */
    protected $SETTINGS_API_HOST    = "https://block.io/";
    protected $SETTINGS_API_PATH    = "api/v1/";

    /* vars */
    private $APIKEY                 = NULL;
    private $APIKEY_VALID           = false;


    /* construcor */
    public function __construct($APIKEY = NULL) {

        // SETUP APIKEY
        if($APIKEY != NULL) {

            $this->APIKEY = $APIKEY;

            // VERIFY APIKEY
            if($this->verify_apikey() != true) {
                echo "Your API KEY is invalid!";
                exit();
            }

            $this->APIKEY_VALID = true;

        }

    }
	
	/* server_request */
    private function server_request($request, $verify_apikey = false, $args=null) {

        if($this->APIKEY_VALID == false && $verify_apikey == false) {
            echo "You need an valid API KEY to use this function!";
            exit();
        }

        $request_build = $this->SETTINGS_API_HOST.$this->SETTINGS_API_PATH;

		$request_build .= $request;
       	$request_build .= "/?api_key=".$this->APIKEY;

		if ($args != null)
		{
			$request_build .= $args;
		}

        $response = @file_get_contents($request_build);

        if(!empty($response)) {
            return json_decode($response, true);
        }

        return $response;

    }
	
	/* verify_apikey */
    private function verify_apikey() {

        $response = $this->server_request("get_balance", true, null);

        if(empty($response)) {
            return false;
        }

        return true;

    }
	
	/* API */
	public function get_balance()
	{
		$response = $this->server_request("get_balance");
		return $response->data->available_balance;
	}
	
	public function get_new_address($label = null)
	{
		if ($label == null)
		{
			$response = $this->server_request("get_new_address");
		}
		else if ($label != null)
		{
			$response = $this->server_request("get_new_address",false, "&label=".$label);	
		}
		return $response[data][address];
	}
	
	public function withdraw($amount, $payment_address, $pin)
	{
		$response = $this->server_request("withdraw", false, "&amount=".$amount."&payment_address=".$payment_address."&pin=".$pin);
		return $response[status];
	}
	
	public function get_my_addresses()
	{
		$response = $this->server_request("get_my_addresses");
		return $response[data][addresses];
	}	
	
	public function get_address_balance($address = null, $label = null)
	{
		if ($address != null)
		{
			$response = $this->server_request("get_address_balance", false, "&address=".$address);
		}
		else if ($label != null)
		{
			$response = $this->server_request("get_address_balance", false, "&label=".$address);
		}
		return $response[data][available_balance];
	}
	
	public function get_address_received($address = null, $label = null, $confirmed = true)
	{
		if ($address != null)
		{
			$response = $this->server_request("get_address_received", false, "&address=".$address);
		}
		else if ($label != null)
		{
			$response = $this->server_request("get_address_received", false, "&label=".$label);
		}
		if ($confirmed == true)
		{
			return $response[data][confirmed_received];
		}
		else if ($confirmed == false)
		{
			return $response[data][unconfirmed_received];
		}
	}
	
	public function get_address_by_label($label)
	{
		$response = $this->server_request("get_address_by_label", false, "&label=".$label);
		return $response[data][address];
	}
	
	/*
	 * API - user specific actions
	 */
	 
	 public function create_new_user($label = null)
	 {
	 	if ($label == null)
		{
			$response = $this->server_request("create_user");
		}
		else if ($label != null)
		{
			$response = $this->server_request("create_user", false, "&label=".$label);
		}
		
		return array(address => $response[data][address], id => $response[data][user_id]);
	 }
	
	public function get_user_balance($user_id)
	{
		$response = $this->server_request("get_user_balance", false, "&user_id=".$user_id);
		return $response[data][available_balance];
	}
	
	public function withdraw_from_user($amount, $sendin_id, $payment_address, $pin)
	{
		$response = $this->server_request("withdraw_from_user", false, "&from_user_ids=".$sending_id."&payment_address=".$payment_address."&amount=".$amount."&pin=".$pin);
		return $response[status];
	}
	
	public function make_quicktip($amount, $sending_id, $receiving_id, $pin)
	{
		$response = $this->server_request("withdraw_from_user", false, "&from_user_ids=".$sending_id."&to_user_id=".$receiving_id."&amount=".$amount."&pin=".$pin);
		return $response[status];
	}
	
}

?>
