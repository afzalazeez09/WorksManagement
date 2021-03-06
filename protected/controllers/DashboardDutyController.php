<?php

class DashboardDutyController extends DutyController
{
	protected function newButton()
	{
		
	}
	
	public function actionUpdate($id, $model = NULL) {
		if(isset($_POST['DashboardDuty'])) {
			$_POST['Duty'] = $_POST['DashboardDuty'];
		}
		
		parent::actionUpdate($id);
	}
	
	// redirect to admin - bypass the dutyController version as don't want to limit by task
	protected function adminRedirect($model, $sortByNewest = false, $params = array()) {
		static::staticAdminRedirect($model, $sortByNewest);
	}

	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('admin', 'view', 'update'),
				'users'=>array('@'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}
	
	public function setTabs($model = NULL, &$tabs = NULL) {
		$dashboardController = new DashboardController(NULL);
		$dashboardController->setTabs(NULL);
		static::$tabs = $dashboardController->tabs;
		// if update or view
		if(!empty($model))
		{
			$tabs = array();
			// add tab to  update duty
			$this->addTab(DashboardDuty::getNiceName(NULL, $model), 'DashboardDuty', Yii::app()->controller->action->id, array('id' => $model->id), static::$tabs[], TRUE);
			// add tab to view associated tasks
			$this->addTab(DashboardTask::getNiceNamePlural(), 'DashboardTask', 'admin', array(
				'duty_data_id' => $model->duty_data_id,
				'duty_id' => $model->id,
				), static::$tabs[sizeof(static::$tabs) - 1]);
		}
		
	}
	

}