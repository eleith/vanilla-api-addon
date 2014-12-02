<?php if (!defined('APPLICATION')) exit();

class SessionAPIController extends APIController 
{
	

	public $Uses = array('Form', 'Database', 'CategoryModel', 'DiscussionModel', 'CommentModel','UserModel');
	//TODO should allow for only one catgories to be looked at
	public function Index()
	{
		
		$Session 	= Gdn::Session();

		if($Session->User != False)
			$this->SetJSON("user", array("TransientKey"=>$Session->TransientKey(), "UserID"=>$Session->UserID, "Name"=>$Session->User->Name, "User"=>True));
		else
			$this->SetJSON("user", array("TransientKey"=>$Session->TransientKey(), "UserID"=>0, "User"=>False));

		$this->Render();
	}


	public function Login(){

		$Username = GetIncomingValue('user', 'admin');
		$Password = GetIncomingValue('pass', 'pass');

		$UserModel = new UserModel();
		$User = $UserModel->GetByEmail($Username);

		if (!$User) {
		  $User = $UserModel->GetByUsername($Username);
		}

		$Result = FALSE;
		if ($User) {
		  // Check the password.
		  $PasswordHash = new Gdn_PasswordHash();
		  $Result = $PasswordHash->CheckPassword($Password, val('Password', $User), val('HashMethod', $User));
		  //print_r($User);exit;
		
		  if ($Result) {
		  	$Session 	= Gdn::Session();
		  	Gdn::Session()->Start($User->UserID, TRUE, TRUE);
		  	$this->SetJSON("user", array("TransientKey"=>$User->Attributes['TransientKey'], "UserID"=>$User->UserID, "Name"=>$User->Name, "User"=>$Result));
		  } else {
		  	$this->SetJSON("user", array("TransientKey"=>false, "UserID"=>0, "User"=>False));
		  }

		}
		
		$this->Render();
		Gdn::Session()->End();

		//echo ($Result) ? 'Success' : 'Failure';
	}


}

?>
