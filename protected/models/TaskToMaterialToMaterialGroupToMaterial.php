<?php

/**
 * This is the model class for table "task_to_material_to_material_group_to_material".
 *
 * The followings are the available columns in table 'task_to_material_to_material_group_to_material':
 * @property string $id
 * @property string $task_to_material_id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property Staff $staff
 * @property MaterialGroupToMaterial $materialGroup
 */
class TaskToMaterialToMaterialGroupToMaterial extends ActiveRecord
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
		return array(
			array('task_to_assembly_id, quantity, task_id, material_group_id, material_id, staff_id', 'required'),
			array('quantity, material_group_id, material_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_to_assembly_id, task_id, task_to_material_id', 'length', 'max'=>10),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
			'material' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'materialGroup' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_group_id'),
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
}