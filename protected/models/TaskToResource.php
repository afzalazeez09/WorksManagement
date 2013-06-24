<?php

/**
 * This is the model class for table "tbl_task_to_resource".
 *
 * The followings are the available columns in table 'tbl_task_to_resource':
 * @property string $id
 * @property string $task_id
 * @property string $resource_data_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property ResourceData $resourceData
 */
class TaskToResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	public $searchResource;
	public $searchTaskQuantity;
	public $searchTotalDuration;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	public $quantity;
	public $duration;
	public $start;
	public $description;
	public $resource_to_supplier_id;
	public $searchLevel;
	public $resource_id;
	public $level;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_id, resource_id, quantity, duration', 'required'),
			array('level, resource_id, resource_to_supplier_id, quantity', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('task_id', 'length', 'max'=>10),
			array('resource_to_supplier_id', 'safe'),
			array('start, duration', 'date', 'format'=>'H:m'),
		));
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'resourceData' => array(self::BELONGS_TO, 'ResourceData', 'resource_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'searchSupplier' => 'Supplier',
			'searchResource' => 'Resource',
			'resource_to_supplier_id' => 'Supplier',
			'resource_id' => 'Resource type',
			'description' => 'Resource type',
			'duration' => 'Duration (HH:mm)',
			'start' => 'Start time (HH:mm)',
			'searchTaskQuantity' => 'Task quantity',
			'searchTotalDuration' => 'Total time',
			'searchLevel' => 'Level',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'resourceData.resource_id AS resource_id',
			'resource.description AS searchResource',
			'supplier.name AS searchSupplier',
			'resourceData.quantity AS quantity',
			'task.quantity AS searchTaskQuantity',
			'SEC_TO_TIME(TIME_TO_SEC(duration) * resourceData.quantity * task.quantity) AS searchTotalDuration',
			'resourceData.duration AS duration',
			'resourceData.start AS start',
			'level0.name AS searchLevel',
		);

		// where
		$criteria->compare('resource.description',$this->searchResource,true);
		$criteria->compare('supplier.name',$this->searchSupplier,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('t.searchTaskQuantity',$this->searchTaskQuantity);
		$criteria->compare('resourceData.quantity * task.quantity',$this->searchTotalDuration);
		$criteria->compare('duration',Yii::app()->format->toMysqlTime($this->duration));
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('level0.name',$this->searchLevel,true);
		$criteria->compare('t.task_id',$this->task_id);
		
		//  with
		$criteria->with = array(
			'task',
			'resourceData',
			'resourceData.resource',
			'resourceData.resourceToSupplier',
			'resourceData.resourceToSupplier.supplier',
			'resourceData.level0',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchResource';
        $columns[] = static::linkColumn('searchSupplier', 'ResourceToSupplier', 'resource_to_supplier_id');
		$columns[] = 'quantity';
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'duration:time';
		$columns[] = 'searchTotalDuration:time';
		$columns[] = 'start:time';
		$columns[] = 'searchLevel';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchResourceToSupplier',
			'description',
			'quantity',
			'searchTaskQuantity',
			'duration',
			'searchTotalDuration',
			'start',
		);
	}
	
	static function getDisplayAttr()
	{
		return array('resourceData->resourceToSupplier->resource->description');
	}

	public function beforeSave()
	{
		$this->resourceData->quantity = $this->quantity;
		$this->resourceData->duration = $this->duration;
		$this->resourceData->start = $this->start;
		$this->resourceData->resource_id = $this->resource_id;
		$this->resourceData->level = $this->level;

		return parent::beforeSave();
	}

	public function afterFind() {
		$this->resource_to_supplier_id = $this->resourceData->resource_to_supplier_id;
		$this->quantity = $this->resourceData->quantity;
		$this->duration = $this->resourceData->duration;
		$this->start = $this->resourceData->start;
		$this->resource_id = $this->resourceData->resource_id;
		$this->level = $this->resourceData->level;
		
		parent::afterFind();
	}

	public function insertResourceData()
	{
		if($this->level === null)
		{
			$this->level = Planning::planningLevelTaskInt;
		}
// TODO: a lot of this repeated in resource controller - abstract out - perhaps into PlanningController static function
		// ensure existance of a related ResourceData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		if(($level = $this->level) == Planning::planningLevelTaskInt)
		{
			$planning_id = $this->task_id;
		}
		else
		{
			// get the desired ansestor
			$planning = Planning::model()->findByPk($this->task_id);

			while($planning = $planning->parent)
			{
				if($planning->level == $level)
				{
					break;
				}
			}
			if(empty($planning))
			{
				throw new Exception();
			}

			$planning_id = $planning->id;
		}
		// try insert and catch and dump any error - will ensure existence
		try
		{
			$resourceData = new ResourceData;
			$resourceData->planning_id = $planning_id;
			$resourceData->resource_id = $this->resource_id;
			$resourceData->resource_to_supplier_id = $this->resource_to_supplier_id;
			$resourceData->level = $level;
			$resourceData->quantity = $this->quantity;
			$resourceData->duration = $this->duration;
			$resourceData->start = $this->start;
			$resourceData->updated_by = Yii::app()->user->id;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$resourceData->insert();
		}
		catch (CDbException $e)
		{
			// retrieve the ResourceData
			$resourceData = ResourceData::model()->findByAttributes(array(
				'planning_id'=>$planning_id,
				'resource_id'=>$this->resource_id,
			));
			// update the resource data
// TODO: will have issue here if level changes then planning_id will need to change also may need to converge or diverge
//			$resourceData->planning_id = $planning_id;
			$resourceData->resource_id = $this->resource_id;
			$resourceData->resource_to_supplier_id = $this->resource_to_supplier_id;
//			$resourceData->level = $level;
			$resourceData->quantity = $this->quantity;
			$resourceData->duration = $this->duration;
			$resourceData->start = $this->start;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$resourceData->dbCallback('save');
		}

		// link this Resource to the ResourceData
		$this->resource_data_id = $resourceData->id;
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		
		// ensure the related items are set
		$this->beforeSave();

		// ensure the ResourceData has correct level by inserting a new one if necassary or linking to correct
		$this->insertResourceData();

		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		$this->insertResourceData();
	
		return parent::createSave($models);
	}

}

?>