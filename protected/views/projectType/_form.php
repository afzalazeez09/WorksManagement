<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	 echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>64));

	ClientController::listWidgetRow($model, $form, 'client_id');
	
	ProjectController::listWidgetRow($model, $form, 'template_project_id');

$this->endWidget();

?>
