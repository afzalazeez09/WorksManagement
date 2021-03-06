<?php

abstract class CustomFieldActiveRecord extends ActiveRecord
{
	protected $evalCustomFieldPivots;
	protected $evalClassEndToCustomFieldPivot;
	protected $evalColumnCustomFieldModelTemplateId;
	protected $evalColumnEndId;	// e.g. task_id, project_id, duty_data_id
	protected $evalThisColumnEndId = 'id';	// the column name in this model whos value gets set in $evalColumnEndId
											// this is needed in duty as dealing with duty model but need to use duty_data_id

	// needed as using a view to concat custom columns in admin view
	public function primaryKey()
	{
		return 'id';
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array(), $runValidation=true)
	{
		if($saved = $this->dbCallback('save', array($runValidation)))
		{
			// attempt creation of customValues
			$saved &= $this->createCustomFields($models, $runValidation);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of customValues
			$saved &= $this->updateCustomFields($models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}

	protected function createCustomFields(&$models=array(), $runValidation=true)
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;

		// loop thru all custom fields pivots
		foreach(eval("return {$this->evalCustomFieldPivots};") as $customFieldPivot)
		{
			$endToCustomFieldPivot = new $this->evalClassEndToCustomFieldPivot;
			$endToCustomFieldPivot->{$this->evalColumnCustomFieldModelTemplateId} = $customFieldPivot->id;

			$endToCustomFieldPivot->{$this->evalColumnEndId} = $this->{$this->evalThisColumnEndId};
			if(isset($_POST[get_class($endToCustomFieldPivot)][$endToCustomFieldPivot->{$this->evalCustomFieldPivot}->custom_field_id]['custom_value']))
			{
				$endToCustomFieldPivot->custom_value=$_POST[get_class($endToCustomFieldPivot)][$endToCustomFieldPivot->{$this->evalCustomFieldPivot}->custom_field_id]['custom_value'];
			}
			else
			{
				$endToCustomFieldPivot->setDefault($customFieldPivot->customField);
			}

			// attempt save
			$saved &= $endToCustomFieldPivot->dbCallback('save', array($runValidation));
			// record any errors
			$models[] = $endToCustomFieldPivot;
		}
		
		return $saved;
	}

	public function updateCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all associated customFields
		foreach(eval("return {$this->evalEndToCustomFieldPivots};") as $endToCustomFieldPivot)
		{
			$endToCustomFieldPivot->custom_value=$_POST[get_class($endToCustomFieldPivot)][$endToCustomFieldPivot->{$this->evalCustomFieldPivot}->custom_field_id]['custom_value'];
			$saved &= $endToCustomFieldPivot->updateSave($models);
		}

		return $saved;
	}

}
?>