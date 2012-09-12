<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $level
 * @property integer $project_type_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Day[] $days
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property Schedule $id0
 * @property ProjectLevel $level0
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property Task[] $tasks
 */
class Project extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchName;
	/**
	 * @var integer $client_id may be passed via get for search
	 */
	public $client_id;
	
	public $scheduleName;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_type_id, staff_id', 'required'),
			array('project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level', 'length', 'max'=>10),
			array('travel_time_1_way, critical_completion, planned, client_id, scheduleName', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, travel_time_1_way, critical_completion, planned, searchName, searchStaff, searchProjectType', 'safe', 'on'=>'search'),
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
			'days' => array(self::HAS_MANY, 'Day', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'id0' => array(self::BELONGS_TO, 'Schedule', 'id'),
			'level0' => array(self::BELONGS_TO, 'ProjectLevel', 'level'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project',
			'travel_time_1_way' => 'Travel time 1 way',
			'critical_completion' => 'Critical completion',
			'planned' => 'Planned',
			'project_type_id' => 'Project type',
			'searchName' => 'Project name',
			'scheduleName' => 'Project name',
			'searchProjectType' => 'Project type',
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
			't.id',
			'id0.name AS searchName',
			'travel_time_1_way',
			't.critical_completion',
			't.planned',
			't.project_type_id',	// though not displayed, needed to get id for link field
			'projectType.description AS searchProjectType',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('searchName',$this->searchName,true);
		$criteria->compare('t.travel_time_1_way',$this->travel_time_1_way);
		$criteria->compare('t.critical_completion',Yii::app()->format->toMysqlDate($this->critical_completion));
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
		$criteria->compare('projectType.description', $this->searchProjectType, true);
		$criteria->compare('client.id', $this->client_id);

		// join
		$criteria->with = array(
			'projectType',
			'projectType.client',
			'id0',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'searchName';
		$columns[] = static::linkColumn('searchProjectType', 'ProjectType', 'project_type_id');
		$columns[] = 'travel_time_1_way';
		$columns[] = 'critical_completion';
		$columns[] = 'planned';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectType', 'searchName');
	}

	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{

		// if we don't have this fk attribute set
		if(empty($this->project_type_id) && empty($this->client_id))
		{
			$niceNameLower =  strtolower(static::getNiceName());
			throw new CHttpException(400, "No $niceNameLower identified, you must get here from the {$niceNameLower}s page");
		}
		// otherwise return the fk
		else
		{
			return $parentForeignKey;
		}
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