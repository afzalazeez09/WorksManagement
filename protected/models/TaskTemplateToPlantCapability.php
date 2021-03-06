<?php

/**
 * This is the model class for table "tbl_task_template_to_plant_capability".
 *
 * The followings are the available columns in table 'tbl_task_template_to_plant_capability':
 * @property integer $id
 * @property integer $task_template_to_plant_id
 * @property integer $plant_capability_id
 * @property integer $plant_to_supplier_to_plant_capability_id
 * @property integer $plant_to_supplier_id
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskTemplateToPlant $taskTemplateToPlant
 * @property PlantToSupplierToPlantCapability $plantToSupplier
 * @property PlantToSupplierToPlantCapability $plantToSupplierToPlantCapability
 * @property PlantToSupplierToPlantCapability $plantCapability
 */
class TaskTemplateToPlantCapability extends ActiveRecord
{
	public $searchPlantCapability;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplateToPlant' => array(self::BELONGS_TO, 'TaskTemplateToPlant', 'task_template_to_plant_id'),
            'plantToSupplier' => array(self::BELONGS_TO, 'PlantToSupplier', 'plant_to_supplier_id'),
            'plantToSupplierToPlantCapability' => array(self::BELONGS_TO, 'PlantToSupplierToPlantCapability', 'plant_to_supplier_to_plant_capability_id'),
            'plantCapability' => array(self::BELONGS_TO, 'PlantCapability', 'plant_capability_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlantCapability', $this->searchPlantCapability, 'plantCapability.description', true);

		// with
		$criteria->with = array(
			'plantCapability',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchPlantCapability';
		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchPlantCapability',
		);
	}

}