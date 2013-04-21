<?php

/**
 * This is the model class for table "task_to_material_to_task_type_to_material_group".
 *
 * The followings are the available columns in table 'task_to_material_to_task_type_to_material_group':
 * @property string $id
 * @property string $task_to_material_id
 * @property integer $material_group_to_material_id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $task_type_to_material_group_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property MaterialGroupToMaterial $materialGroupToMaterial
 * @property TaskTypeToMaterialGroup $materialGroup
 * @property TaskTypeToMaterialGroup $taskTypeToMaterialGroup
 */
class TaskToMaterialToTaskTypeToMaterialGroup extends ActiveRecord
{
	public $task_id;
	public $quantity;
	public $task_to_assembly_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->customValidators + array(
			array('task_type_to_material_group_id, task_to_assembly_id, quantity, task_id, material_group_to_material_id, material_group_id, material_id, staff_id', 'required'),
			array('task_type_to_material_group_id, quantity, material_group_id, material_id, material_group_to_material_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_to_assembly_id, task_id, task_to_material_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		$this->setCustomValidatorsRange(AssemblyToMaterialGroup::model()->findByPk($this->task_type_to_material_group_id));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
			'material' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_id'),
			'materialGroupToMaterial' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_group_to_material_id'),
			'materialGroup' => array(self::BELONGS_TO, 'TaskTypeToMaterialGroup', 'material_group_id'),
			'taskTypeToMaterialGroup' => array(self::BELONGS_TO, 'TaskTypeToMaterialGroup', 'task_type_to_material_group_id'),
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'materialGroup->materialGroup->description',
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'task_to_material_id' => 'Task To Material',
			'material_group_id' => 'Material Group',
			'material_group_to_material_id' => 'Material Group',
			'material_id' => 'Material',
		);
	}
	
	public function assertFromParent($modelName = null) {
		Controller::$nav['update']['TaskToMaterial'] = $this->task_to_material_id;;
		
		// need to trick it here into using task to material model instead as this model not in navigation hierachy
		if(!empty($this->task_to_material_id))
		{
			$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
			return $taskToMaterial->assertFromParent('TaskToMaterial');
		}
		
		return parent::assertFromParent($modelName);
	}
	
	public function afterFind() {
		
		$taskToMaterialId = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$this->quantity = $taskToMaterialId->quantity;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		// first need to save the TaskToAssembly record as otherwise may breach a foreign key constraint - this has on update case
		$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$taskToMaterial->material_id = $this->material_id;
		
		if($saved = $taskToMaterial->updateSave($models))
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}

	public function createSave(&$models=array())
	{
		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_POST['TaskToMaterialToTaskTypeToMaterialGroup'];
		// filler - unused in this context but necassary in Material model
		$taskToMaterial->store_id = 0;

		if($saved = $taskToMaterial->createSave($models))
		{
			$this->task_to_material_id = $taskToMaterial->id;
			$saved &= parent::createSave($models);
		}

		return $saved;
	}

}