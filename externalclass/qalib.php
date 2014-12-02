<?php
 
class MyClass
{

	public $Key = false;

	public $TransientKEY = false;
	public $UserID = false;
	public $URL = "example.com/api";

	public function __construct($user=false,$pass=false)
	{

		if ($user && $pass){
			return $this->login($user,$pass);
		}
		return false;

	}

	public function getTransientKey() {
		return $this->TransientKey;
	}

	public function getUserID(){
		return $this->UserID;
	}

	public function curl_post($fields = array(), $url =''){
		$fields_string = '';

		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function curl_get($url=''){

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch); 
		curl_close($ch); 
		return $output;     
	}

	/**
	* Login function that retrieves TransientKey
	*
	* @param (string) (user) username
	* @param (string) (pass) password
	* @return (TransientKey)
	*/
	public function login($user='',$pass='') {


		$url = $this->URL."/sessionapi/login?user=".$user."&pass=".$pass;

	    $json = json_decode($this->curl_get($url));

	    $this->TransientKey = $json->user->TransientKey;
	    $this->UserID = $json->user->UserID;

	    $this->Key = $this->UserID."-".$this->TransientKey;

	    return $json->user;
	}


	/**
		DISCUSSIONS
	*/

	public function addDiscussion($CategoryID = false,$Name = false,$Body = false){


		//set POST variables
		$url = $this->URL."/discussionapi/add";

		$fields = array(
						'TransientKey' => $this->TransientKey,
						'UserID' => $this->UserID,
						'CategoryID' => urlencode($CategoryID),
						'Body' => urlencode($Body),
						'Name' => urlencode($Name),
						'Type' => 'Question'
						);

	return $this->curl_post($fields,$url);

	}

	public function removeDiscussion($CategoryID = false,$DiscussionID = false){

		//set POST variables
		$url = $this->URL."/discussionapi/remove";

		$fields = array(
						'TransientKey' => $this->TransientKey,
						'UserID' => $this->UserID,
						'CategoryID' => urlencode($CategoryID),
						'DiscussionID' => urlencode($DiscussionID)
						);

		return $this->curl_post($fields,$url);

	}

	/**
		COMMENTS
	*/

	public function addComment($DiscussionID = false,$CategoryID = false,$Body = false){

		//set POST variables
		$url = $this->URL."/commentapi/add";

		$fields = array(
						'TransientKey' => $this->TransientKey,
						'UserID' => $this->UserID,
						'CategoryID' => urlencode($CategoryID),
						'DiscussionID' => urlencode($DiscussionID),
						'Body' => urlencode($Body)
						);

	return $this->curl_post($fields,$url);

	}

	public function removeComment($CategoryID = false,$CommentID = false){

		//set POST variables
		$url = $this->URL."/commentapi/remove";

		$fields = array(
						'TransientKey' => $this->TransientKey,
						'UserID' => $this->UserID,
						'CategoryID' => urlencode($CategoryID),
						'CommentID' => urlencode($CommentID)
						);

		return $this->curl_post($fields,$url);

	}

}
 
 
?>