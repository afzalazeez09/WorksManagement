<?php

/**
 * This is the model class for table "crew".
 *
 * The followings are the available columns in table 'crew':
 * @property string $id
 * @property string $level
 * @property string $day_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Schedule $id0
 * @property CrewLevel $level0
 * @property Staff $staff
 * @property Day $day
 * @property Task[] $tasks
 */
class Crew extends ActiveRecord
{
	public $searchName;
	
	public $scheduleName;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'crew';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('day_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level, day_id', 'length', 'max'=>10),
			array('scheduleName', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, day_id, staff_id', 'safe', 'on'=>'search'),
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
			'id0' => array(self::BELONGS_TO, 'Schedule', 'id'),
			'level0' => array(self::BELONGS_TO, 'CrewLevel', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'day' => array(self::BELONGS_TO, 'Day', 'day_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'Crew',
			'scheduleName' => 'Crew',
		);
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			't.id',
			'id0.name AS searchName',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('searchName',$this->searchName,true);
		$criteria->compare('t.day_id',$this->day_id);

		// join
		$criteria->with = array(
			'id0',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'searchName';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchName');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->name';

		return $displaAttr;
	}

	public function afterFind() {
		$this->scheduleName = $this->id0->name;
		
		parent::afterFind();
	}

}

?>