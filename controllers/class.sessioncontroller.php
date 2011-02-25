<?php if (!defined('APPLICATION')) exit();

class SessionController extends APIController 
{
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

}

?>
