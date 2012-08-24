<?php

/**
 * This is the model class for table "default_value".
 *
 * The followings are the available columns in table 'default_value':
 * @property string $id
 * @property string $table
 * @property string $column
 * @property string $select
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 */
class DefaultValue extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTableColumn;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DefaultValue the static model class
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
		return 'default_value';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('table, column, select, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('table, column', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchTableColumn, id, table, column, select, staff_id', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Default value',
			'table' => 'Table',
			'column' => 'Column',
			'searchTableColumn' => 'Table/Column',
			'select' => 'Select',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.table',$this->table);
		$criteria->compare('t.column',$this->column);
		$criteria->compare('t.select',$this->select);

		$criteria->select=array(
//			't.id',
			't.table',
			't.column',
			't.select',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'table';
		$columns[] = 'column';
		$columns[] = 'select';
		
		return $columns;
	}

}