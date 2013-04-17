<?php

/**
 * This is the model class for table "day".
 *
 * The followings are the available columns in table 'day':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property string $scheduled
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Crew[] $crews
 * @property Planning $id0
 * @property DayLevel $level0
 * @property Staff $staff
 * @property Project $project
 */
class Day extends ActiveRecord
{
	static $niceNamePlural = 'Days';
	public $searchInCharge;
	public $name;
	public $in_charge_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id', 'required'),
			array('id, level, project_id, in_charge_id', 'length', 'max'=>10),
			array('name, scheduled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, level, searchInCharge, project_id, scheduled', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// class name for the relations automatically generated below.
		return array(
			'crews' => array(self::HAS_MANY, 'Crew', 'day_id'),
			'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
			'level0' => array(self::BELONGS_TO, 'DayLevel', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
		);
	}

	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
			'scheduled' => 'Scheduled',
			'name' => 'Comment',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			'id0.name AS name',
			't.scheduled',
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.scheduled',Yii::app()->format->toMysqlDate($this->scheduled));
		$criteria->compare('t.project_id',$this->project_id);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);

		// join
		$criteria->with = array(
			'id0',
			'id0.inCharge',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('id');
		$columns[] = $this->linkThisColumn('name');
 		$columns[] = 'scheduled:date';
		$columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge', 'name');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='scheduled';

		return $displaAttr;
	}

	public function afterFind() {
		$this->name = $this->id0->name;
		
		parent::afterFind();
	}

	public function assertFromParent()
	{
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
	}

	public function beforeSave() {

		// ensure no editing unless scehduler
		if(!Yii::app()->user->checkAccess('scheduler'))
		{
			// reset
			$this->scheduled = $this->getOldAttributeValue('scheduled');
			$this->name = $this->getOldAttributeValue('name');
		}
			
		return parent::beforeSave();
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($this->id);
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here
$t= $this->attributes;		
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];

		if($saved = $planning->appendTo(Planning::model()->findByPk($this->project_id)))
		{
			$this->id = $planning->id;
			$saved = parent::createSave($models);

			// add a Crew
			$crew = new Crew;
			$crew->day_id = $this->id;
			$saved = $crew->createSave($models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

}

?>