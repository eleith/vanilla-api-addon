<?php if (!defined('APPLICATION')) exit();

class DiscussionController extends APIController 
{
   public $Uses = array('Form', 'Database', 'CategoryModel', 'DiscussionModel', 'CommentModel');

	public function Index()
	{
		$Limit			= GetIncomingValue('limit', 5);
		$Offset			= GetIncomingValue('offset', 0);
		$DiscussionID	= GetIncomingValue('id', 0);
		$Session			= Gdn::Session();
		$Discussion		= $this->DiscussionModel->GetID($DiscussionID);

		if($Discussion != False && $Session->CheckPermission('Vanilla.Discussions.View', $Discussion->CategoryID))
		{
			$this->SetJSON("discussion", $Discussion);

			if($Discussion->CountComments > 0)
			{
				$Comments = $this->CommentModel->Get($DiscussionID, $Limit, $Offset)->Result();
				$this->SetJSON("comments", $Comments);
			}
		}

		$this->Render();
   }

	/**
    * Create a discussion.
    * @param int The category id to add the discussion to.
    */
   public function Add()
	{
      $Session = Gdn::Session();
		$Errors = array();

      // Set the model on the form.
      $this->Form->SetModel($this->DiscussionModel);

      if($this->Form->AuthenticatedPostBack() === TRUE) 
		{
         $FormValues = $this->Form->FormValues();

         // Check category permissions
			if(!$Session->CheckPermission('Vanilla.Discussions.Add', $FormValues['CategoryID']))
			   $Errors[] = 'You do not have permission to start discussions in this category';
			else
		   	$DiscussionID = $this->DiscussionModel->Save($FormValues, $this->CommentModel);
		}
		else
			$Errors[] = 'You do not have credentials to post as this user';

		// Return the form errors
		if(count($Errors) > 0)
			$this->SetJSON("Errors", $Errors);

		$this->Render();
	}

}

?>
