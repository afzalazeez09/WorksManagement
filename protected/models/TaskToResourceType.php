<?php

/**
 * This is the model class for table "task_to_resource_type".
 *
 * The followings are the available columns in table 'task_to_resource_type':
 * @property string $id
 * @property string $task_id
 * @property integer $resource_type_id
 * @property string $level
 * @property string $resource_data_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property ResourceType $resourceType
 * @property Staff $staff
 * @property ResourceData $resourceData
 * @property ResourceData $level0
 */
class TaskToResourceType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	public $quantity;
	public $hours;
	public $start;
	public $description;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_to_resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, resource_type_id, quantity, hours, staff_id', 'required'),
			array('level, resource_type_id, quantity, hours, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('task_id', 'length', 'max'=>10),
			array('start', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, task_id, searchTask, description, quantity, hours, start, searchStaff', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'resourceType' => array(self::BELONGS_TO, 'ResourceType', 'resource_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'resourceData' => array(self::BELONGS_TO, 'ResourceData', 'resource_data_id'),
			'level0' => array(self::BELONGS_TO, 'ResourceData', 'level'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task to resource type',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'resource_type_id' => 'Resource type',
			'description' => 'Resource type',
			'quantity' => 'Quantity',
			'hours' => 'Hours',
			'start' => 'Start',
			'level' => 'Level',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			't.resource_type_id',
			'resourceType.description AS description',
			'resourceData.quantity AS quantity',
			'resourceData.hours AS hours',
			'resourceData.start AS start',
			't.level',
		);

		// where
		$criteria->compare('description',$this->description,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('hours',$this->hours);
		$criteria->compare('start',$this->start);
		$criteria->compare('t.level',$this->level);
		$criteria->compare('t.task_id',$this->task_id);
		
		//  join
		$criteria->with = array(
			'resourceType',
			'resourceData',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('description', 'ResourceType', 'resource_type_id');
		$columns[] = 'quantity';
		$columns[] = 'hours';
		$columns[] = 'start';
		$columns[] = 'level';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'description', 'quantity', 'hours', 'start');
	}
	
	static function getDisplayAttr()
	{
		return array('resourceType->description');
	}

	public function beforeSave()
	{
		$this->resourceData->quantity = $this->quantity;
		$this->resourceData->hours = $this->hours;
		$this->resourceData->start = $this->start;

		return parent::beforeSave();
	}

	public function afterFind() {
		$this->quantity = $this->resourceData->quantity;
		$this->hours = $this->resourceData->hours;
		$this->start = $this->resourceData->start;
		
		parent::afterFind();
	}

}

?>