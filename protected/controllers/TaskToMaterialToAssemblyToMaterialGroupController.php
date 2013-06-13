<?php

class TaskToMaterialToAssemblyToMaterialGroupController extends Controller
{
// TODO: Repeated in TaskToMaterialToTaskTemplateToMaterialGroupController	
	protected function createRender($model, $models, $modal_id)
	{
		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_GET[$this->modelName];
		$taskToMaterial->id = $model->task_to_material_id;
		$taskToMaterial->assertFromParent();

		// set heading
//		$this->heading = TaskToMaterial::getCreateLabel();
		
		// set tabs
		$this->tabs = $taskToMaterial;

		// set breadcrumbs
	//	$this->breadcrumbs = TaskToMaterialController::getBreadCrumbTrail($this->heading);
		
		echo $this->render('_form',array(
			'model'=>$model,
			'models'=>$models,
		));
	}

	protected function updateRedirect($model) {
		$this->createRedirect($model);
	}

	protected function createRedirect($model)
	{
		// go to admin view
		$taskToMaterial = TaskToMaterial::model()->findByPk($model->task_to_material_id);
		$taskToMaterial->assertFromParent();
		
		$params = array("TaskToMaterial/admin") + static::getAdminParams('TaskToMaterial');
		$params['parent_id'] =$taskToMaterial->taskToAssembly->parent_id;
		
		$this->redirect($params);
	}
	
	
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		return TaskToMaterialController::getBreadCrumbTrail('Update');
	}
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {
		$modelName = $this->modelName;

		if($_GET['parent_id'] = $task_to_assembly_id = (isset($model->taskToAssembly) ? $model->taskToAssembly->id : $model->taskToMaterial->taskToAssembly->id))
		{
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(TaskToAssembly::getNiceNamePlural(), $modelName::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;

			static::setUpdateId(NULL, 'TaskToAssembly');

			// if creating or updating
			if($model)
			{
				$lastLabel = isset($_GET['id'])
					? $modelName::getNiceName($_GET['id'])
					: $modelName::getCreateLabel();
				$tabs=array();
				$this->addTab(
					$lastLabel,
					Yii::app()->controller->modelName,
					Yii::app()->controller->action->id,
					$_GET,
					$tabs,
					TRUE
				);
				static::$tabs = array_merge(static::$tabs, array($tabs));
			}

			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
		}
		
	}

	
}
