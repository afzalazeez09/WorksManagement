<?php

/**
 * This is the model class for table "resource_type_to_supplier".
 *
 * The followings are the available columns in table 'resource_type_to_supplier':
 * @property integer $id
 * @property integer $resource_type_id
 * @property integer $supplier_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceData[] $resourceDatas
 * @property ResourceData[] $resourceDatas1
 * @property ResourceType $resourceType
 * @property Supplier $supplier
 * @property Staff $staff
 */
class ResourceTypeToSupplier extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Supplier';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'resource_type_to_supplier';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('resource_type_id, supplier_id, staff_id', 'required'),
			array('resource_type_id, supplier_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchSupplier, resource_type_id, supplier_id, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'resource_type_id'),
			'resourceDatas1' => array(self::HAS_MANY, 'ResourceData', 'resource_type_to_supplier_id'),
			'resourceType' => array(self::BELONGS_TO, 'ResourceType', 'resource_type_id'),
			'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'resource_type_id' => 'Resource Type',
			'supplier_id' => 'Supplier',
			'searchSupplier' => 'Supplier',
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
			't.id',	// needed for delete and update buttons
			'supplier.name AS searchSupplier',
		);

		// where
		$criteria->compare('resource_type_id',$this->resource_type_id);
		$criteria->compare('supplier.name',$this->searchSupplier);

		// join
		$criteria->with = array(
			'supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchSupplier', 'Supplier', 'supplier_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'supplier->name',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchSupplier');
	}
}