<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	$form->textFieldRow('alias');

	$form->textFieldRow('unit_price');

$this->endWidget();

?>