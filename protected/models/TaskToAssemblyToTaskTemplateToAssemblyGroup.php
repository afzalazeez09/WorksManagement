<?php

/**
 * This is the model class for table "tbl_task_to_assembly_to_task_template_to_assembly_group".
 *
 * The followings are the available columns in table 'tbl_task_to_assembly_to_task_template_to_assembly_group':
 * @property string $id
 * @property string $task_id
 * @property string $task_to_assembly_id
 * @property integer $assembly_id
 * @property integer $assembly_group_id
 * @property string $task_template_to_assembly_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskToAssembly $taskToAssembly
 * @property AssemblyGroupToAssembly $assembly
 * @property AssemblyGroupToAssembly $assemblyGroupToAssembly
 * @property TaskTemplateToAssemblyGroup $assemblyGroup
 * @property TaskTemplateToAssemblyGroup $taskTemplateToAssemblyGroup
 * @property TaskToAssembly $task
 */
class TaskToAssemblyToTaskTemplateToAssemblyGroup extends ActiveRecord
{
	use RangeActiveRecordTrait;

	public $quantity;
	public $searchAssemblyGroup;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	/**
	 * @var string label for tab and breadcrumbs when creating
	 */
	static $createLabel = 'Select assembly';
	/**
	 * @var string label on button in create view
	 */
	static $createButtonText = 'Save';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(array('task_to_assembly_id')), array(
			array('quantity', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$rangeModel = TaskTemplateToAssemblyGroup::model()->findByPk($this->task_template_to_assembly_group_id);
		
		$this->setCustomValidatorsFromSource($rangeModel);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'taskTemplateToAssemblyGroup' => array(self::BELONGS_TO, 'TaskTemplateToAssemblyGroup', 'task_template_to_assembly_group_id'),
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAssemblyGroup', $this->searchAssemblyGroup, 'assemblyGroup.description', true);

		$criteria->with = array(
			'assemblyGroup',
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAssemblyGroup',
		);
	}
 
	public function assertFromParent($modelName = null) {
		
		// need to trick it here into using task to assembly model instead as this model not in navigation hierachy
		if(!empty($this->task_to_assembly_id))
		{
			Controller::setUpdateId($this->task_to_assembly_id, 'TaskToAssembly');
			$taskToAssembly = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
			return $taskToAssembly->assertFromParent('TaskToAssembly');
		}
		elseif(!empty($this->task_id))
		{
			Controller::setUpdateId($this->task_id, 'Task');
			$task = Task::model()->findByPk($this->task_id);
			return $task->assertFromParent('Task');
		}
		
		return parent::assertFromParent($modelName);
	}
	
	public function afterFind() {
		
		// otherwise our previous saved quantity
		if($task_to_assembly_id = TaskToAssembly::model()->findByPk($this->task_to_assembly_id))
		{
			$this->quantity = $task_to_assembly_id->quantity;
			$this->task_id = $task_to_assembly_id->task_id;
		}

		parent::afterFind();
	}

// TODO: repetition
	public function updateSave(&$models = array()) {
		$saved = true;
		
		$taskToAssembly = $this->task_to_assembly_id
			? $this->taskToAssembly
			: new TaskToAssembly;

		$taskToAssembly->attributes = $_POST[__CLASS__];
		// filler - unused in this context but necassary in Assembly model
		$taskToAssembly->standard_id = 0;
		
		// if existing selection made in past
		if($taskToAssembly->id)
		{
			$saved = $taskToAssembly->delete();
			$taskToAssembly->id = null;
			$taskToAssembly->isNewRecord = true;
		}

		if($taskToAssembly->assembly_id)
		{
			$saved = $taskToAssembly->createSave($models);
			$this->task_to_assembly_id = $taskToAssembly->id;
		}
		
		if($saved)
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}
	
	public function delete()
	{
		$return = parent::delete();

		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_assembly WHERE id = :id');
		$command->bindParam(':id', $temp = $this->task_to_assembly_id);
		$command->execute();
		
		return $return;
	}

}