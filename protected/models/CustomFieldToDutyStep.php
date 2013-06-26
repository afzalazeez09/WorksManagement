<?php

/**
 * This is the model class for table "tbl_custom_field_to_duty_step".
 *
 * The followings are the available columns in table 'tbl_custom_field_to_duty_step':
 * @property integer $id
 * @property integer $duty_step_id
 * @property integer $custom_field_id
 * @property integer $custom_field_duty_step_category_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property CustomField $customField
 * @property User $updatedBy
 * @property CustomFieldDutyStepCategory $dutyStep
 * @property CustomFieldDutyStepCategory $customFieldDutyStepCategory
 * @property DutyDataToCustomFieldToDutyStep[] $dutyDataToCustomFieldToDutySteps
 */
class CustomFieldToDutyStep extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom field';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyStep;
	public $searchCustomFieldDutyStep;
	public $searchCustomField;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('custom_field_id, custom_field_duty_step_category_id', 'required'),
			array('duty_step_id, custom_field_id, custom_field_duty_step_category_id', 'numerical', 'integerOnly'=>true),
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
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyStep' => array(self::BELONGS_TO, 'CustomFieldDutyStepCategory', 'duty_step_id'),
            'customFieldDutyStepCategory' => array(self::BELONGS_TO, 'CustomFieldDutyStepCategory', 'custom_field_duty_step_category_id'),
            'customFieldProjectCategory' => array(self::BELONGS_TO, 'CustomFieldProjectCategory', 'custom_field_project_category_id'),
            'dutyDataToCustomFieldToDutySteps' => array(self::HAS_MANY, 'DutyDataToCustomFieldToDutyStep', 'custom_field_to_duty_step_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'duty_step_id' => 'Duty step',
			'searchDutyStep' => 'Duty step',
			'custom_field_duty_step_category_id' => 'Custom field set',
			'searchCustomFieldDutyStep' => 'Custom field set',
			'custom_field_id' => 'Custom field',
			'searchCustomField' => 'Custom field',
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
			't.custom_field_duty_step_category_id',
			't.custom_field_id',
			'customField.description AS searchCustomField',
		);

		// where
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.custom_field_duty_step_category_id',$this->custom_field_duty_step_category_id);

		// with 
		$criteria->with = array(
			'customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'customField->description',
		);
	}

	public function beforeSave() {
		$customFieldDutyStepCategory = CustomFieldDutyStepCategory::model()->findByPk($this->custom_field_duty_step_category_id); 
		$this->duty_step_id = $customFieldDutyStepCategory->duty_step_id;
		return parent::beforeSave();
	}

}

?>