<?php if (!defined('APPLICATION')) exit();

class CategoryAPIController extends APIController 
{
   public $Uses = array('Gdn_Format', 'Database', 'CategoryModel', 'DiscussionModel');

	//TODO should allow for only one catgories to be looked at
	public function Index()
	{
		$Session 	= Gdn::Session();
		$categories = array();
		$discussionsPerCategory = 4;
		$DiscussionModel = new DiscussionModel();

		$this->CategoryData = $this->CategoryModel->GetFull();
		$this->CategoryDiscussionData = array();

		foreach($this->CategoryData->Result() as $Category) 
		{
			$this->Category = $Category;

			if($Session->CheckPermission('Vanilla.Discussions.View', $this->Category->CategoryID))
			{
				//TODO be nice if options could be passed to filter 
				// discussions that are closed, sunk, etc etc...
				$this->DiscussionData = $DiscussionModel->Get(0, $discussionsPerCategory, array('d.CategoryID' => $Category->CategoryID));
				$category = array();

				foreach($Category as $key => $value)
					$category[$key] = $value;

				#$category["CategoryURL"] = Gdn::Config('Garden.Domain')."/categories/".$Category->UrlCode;
				$category["CategoryURL"] = Gdn::Request()->Domain()."/categories/".$Category->UrlCode;

				if($this->DiscussionData->NumRows() > 0) 
				{
					$count = 0;
					$discussion = array();
					$category["discussions"] = array();

					foreach($this->DiscussionData->Result() as $Discussion)
					{
						foreach($Discussion as $key => $value)
							$discussion[$key] = $value;

						//$discussion["DiscussionURL"] = Gdn::Config('Garden.Domain').'/discussion/'.$Discussion->DiscussionID.'/'.Gdn_Format::Url($Discussion->Name);
						$discussion["DiscussionURL"] = Gdn::Request()->Domain().'/discussion/'.$Discussion->DiscussionID.'/'.Gdn_Format::Url($Discussion->Name);

						if($count++ < $discussionsPerCategory)
							$category["discussions"][] = $discussion;
						else
							break;
					}
				}
	
				$categories[] = $category;
			}
	   }
	
		$this->SetJSON("categories", $categories);
		$this->Render();
   }

	/**
    * Create a discussion.
    * @param int The category id to add the discussion to.
    */
   public function Discussions()
	{
		$CategoryID = 1;
      $Session = Gdn::Session();
      $DiscussionID = isset($this->Discussion) ? $this->Discussion->DiscussionID : '';
      $this->CategoryID = isset($this->Discussion) ? $this->Discussion->CategoryID : $CategoryID;

      if(Gdn::Config('Vanilla.Categories.Use') === TRUE) 
		{
         $CategoryModel = new CategoryModel();

         // Filter to categories that this user can add to
         $CategoryModel->SQL->Distinct()
            ->Join('Permission _p2', '_p2.JunctionID = c.CategoryID', 'inner')
            ->Join('UserRole _ur2', '_p2.RoleID = _ur2.RoleID', 'inner')
            ->BeginWhereGroup()
            ->Where('_ur2.UserID', $Session->UserID)
            ->Where('_p2.`Vanilla.Discussions.Add`', 1)
            ->EndWhereGroup();

         $this->CategoryData = $CategoryModel->GetFull();
      }
      
      if(isset($this->Discussion)) 
		{
         if ($this->Discussion->InsertUserID != $Session->UserID)
            $this->Permission('Vanilla.Discussions.Edit', $this->Discussion->CategoryID);
      }
		else
		{
         $this->Permission('Vanilla.Discussions.Add');
      }
      
      // Set the model on the form.
      $this->Form->SetModel($this->DiscussionModel);

      if($this->Form->AuthenticatedPostBack() === TRUE) 
		{
         $FormValues = $this->Form->FormValues();

         // Check category permissions
			if($this->Form->GetFormValue('Announce', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Announce', $this->CategoryID))
			   $this->Form->AddError('You do not have permission to announce in this category', 'Announce');
			
			if($this->Form->GetFormValue('Close', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Close', $this->CategoryID))
			   $this->Form->AddError('You do not have permission to close in this category', 'Close');
			
			if($this->Form->GetFormValue('Sink', '') != '' && !$Session->CheckPermission('Vanilla.Discussions.Sink', $this->CategoryID))
			   $this->Form->AddError('You do not have permission to sink in this category', 'Sink');
			   
			if(!$Session->CheckPermission('Vanilla.Discussions.Add', $this->CategoryID))
			   $this->Form->AddError('You do not have permission to start discussions in this category', 'CategoryID');
			
			if($this->Form->ErrorCount() == 0) 
			{
		   	$DiscussionID = $this->DiscussionModel->Save($FormValues, $this->CommentModel);
			   $this->Form->SetValidationResults($this->DiscussionModel->ValidationResults());
			}
		}

		if($this->Form->ErrorCount() > 0) 
		{
      	// Return the form errors
			$this->SetJSON("errors", $this->Form->Errors());
		}

		$this->Render();
	}

}

?>
