<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>'task_id'));
	if(isset($model->dutyData))
	{
		// if previously saved
		if($model->dutyData->updated)
		{
			$form->textFieldRow(
				'updated',
				Yii::app()->user->checkAccess('system admin') ? array() : array('readonly'=>'readonly'),
				$model->dutyData);
		}
		else
		{
			UserController::listWidgetRow($model->dutyData, $form, 'responsible', array(), array(), 'Assigned to');
			$form->dropDownListRow('level', Planning::$levels, array(), $model->dutyData);

			// only allow to be checked if dependencies have been checked
			if(Duty::model()->findAll($incompleteDependencies = $model->incompleteDependencies))
			{
				// display a 3 column grid widget with paging showing dependency step, who is responsible if any, and the due date for it
				Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
					'id'=>'dependency-grid',
					'type'=>'striped',
					'dataProvider'=>new CActiveDataProvider('Duty', array('criteria'=>$incompleteDependencies)),
					'columns'=>array(
						'description::Dependent on',
						'derived_assigned_to_name',
						'due',
					),
					'template'=>"{items}\n{pager}",
				));
			}

			$form->checkBoxRow('updated', array(), $model->dutyData);
		}

		$this->widget('CustomFieldWidgets',array(
			'model'=>$model,
			'form'=>$form,
			'relationModelToCustomFieldModelTemplate'=>'dutyDataToDutyStepToCustomField',
			'relationModelToCustomFieldModelTemplates'=>'dutyData->dutyDataToDutyStepToCustomFields',
			'relationCustomFieldModelTemplate'=>'dutyStepToCustomField',
			'relation_category'=>'customFieldDutyStepCategory',
			'categoryModelName'=>'CustomFieldDutyStepCategory',
			'htmlOptions'=>array('onchange'=>"
				id = $(this).attr('id') + '_resource';

				data = {
					'id' : id,
					'val' : $(this).val())
				};

				$(id).load('" . Yii::app()->baseUrl . "Duty/getResources" . "', data);
			"),
		));

		// need to show previous steps custom fields on duty form as disabled
		$this->previousStepsCustomFields($model, $form);
	}

$this->endWidget();

?>