<?php

/**
 * This is the model class for table "generic_project_type".
 *
 * The followings are the available columns in table 'generic_project_type':
 * @property integer $id
 * @property integer $generic_type_id
 * @property integer $generic_project_category_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Genericprojectcategory $genericProjectCategory
 * @property Staff $staff
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 */
class GenericProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchGenericType;
	public $searchGenericProjectCategory;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GenericProjectType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('generic_type_id, staff_id', 'required'),
			array('generic_type_id, generic_project_category_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchGenericType, searchGenericProjectCategory, searchStaff', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'genericProjectCategory' => array(self::BELONGS_TO, 'Genericprojectcategory', 'generic_project_category_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'generic_project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic Project Type',
			'generic_type_id' => 'Generic Type',
			'searchGenericType' => 'Generic Type',
			'generic_project_category_id' => 'Generic Project Category',
			'searchGenericProjectCategory' => 'Generic Project Category',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('genericType.description',$this->searchGenericType);
		$criteria->compare('genericProjectCategory.description',$this->searchGenericProjectCategory);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array('staff','genericType','genericProjectCategory');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'genericType.description AS searchGenericType',
			'genericProjectCategory.description AS searchGenericProjectCategory',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
				'genericType'=>array('description'),
				'genericProjectCategory'=>array('description'),
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchGenericType', 'searchGenericProjectCategory');
	}

}