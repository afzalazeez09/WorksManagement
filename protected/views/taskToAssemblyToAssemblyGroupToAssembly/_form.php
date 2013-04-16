<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('assembly_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_assembly_group_id');

	$assemblyToAssemblyGroup = AssemblyToAssemblyGroup::model()->findByPk($model->assembly_to_assembly_group_id);
	
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array('data-original-title'=>$assemblyToAssemblyGroup->selection_tooltip), array('scopeAssemblyGroup'=>array($model->assembly_group_id)));

	$htmlOptions = array('data-original-title'=>$assemblyToAssemblyGroup->quantity_tooltip);
	if(empty($assemblyToAssemblyGroup->select))
	{
		$form->rangeFieldRow('quantity', $assemblyToAssemblyGroup->minimum, $assemblyToAssemblyGroup->maximum, $htmlOptions, $model);
	}
	else
	{
		// first need to get a list where array keys are the same as the display members
		$list = explode(',', $assemblyToAssemblyGroup->select);

		$form->dropDownListRow('quantity', array_combine($list, $list), $htmlOptions, $model);
	}

$this->endWidget();

?>
