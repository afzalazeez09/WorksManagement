<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		TaskController::listWidgetRow($model, $form, 'task_id');
	}

	TaskTypeToDutyTypeController::listWidgetRow($model, $form, 'task_type_to_duty_type_id', array(), array('scopeTask'=>array($model->task_id)));

	$form->textFieldRow('updated');

	$form->textFieldRow('generic_id');

$this->endWidget();

?>
