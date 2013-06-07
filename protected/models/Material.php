<?php

/**
 * This is the model class for table "tbl_material".
 *
 * The followings are the available columns in table 'tbl_material':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $category
 * @property string $unit
 * @property string $alias
 * @property integer $drawing_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterial[] $assemblyToMaterials1
 * @property User $updatedBy
 * @property Drawing $standard
 * @property Drawing $drawing
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialToClient[] $materialToClients
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskToMaterial[] $taskToMaterials
 */
class Material extends ActiveRecord
{
	public $searchDrawingDescription;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, standard_id', 'required'),
			array('standard_id, drawing_id', 'numerical', 'integerOnly'=>true),
			array('description, alias', 'length', 'max'=>255),
			array('unit', 'length', 'max'=>64),
            array('category', 'safe'),
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
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'standard_id'),
            'assemblyToMaterials1' => array(self::HAS_MANY, 'AssemblyToMaterial', 'material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Drawing', 'standard_id'),
            'drawing' => array(self::BELONGS_TO, 'Drawing', 'drawing_id'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'material_id'),
            'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'material_id'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'material_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'material_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'unit' => 'Unit',
			'standard_id' => 'Standard',
			'drawing_id' => 'Drawing',
			'searchDrawingDescription' => 'Drawing',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.description',
			't.alias',
			't.unit',
			't.category',
			't.drawing_id',
			"CONCAT_WS('$delimiter',
				drawing.alias,
				drawing.description
				) AS searchDrawingDescription",
		);

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.description', $this->description,true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.unit', $this->unit);
		$criteria->compare('t.standard_id', $this->standard_id);
		$criteria->compare('t.category',$this->category,true);
		$boundParam = 'aa';
		$this->compositeCriteria($criteria,
			array(
				'drawing.alias',
				'drawing.description',
			),
			$this->searchDrawingDescription
		);

		$criteria->with = array(
			'drawing',
		);
		
		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'alias';
 		$columns[] = 'category';
		$columns[] = 'unit';
		$columns[] = static::linkColumn('searchDrawingDescription', 'Drawing', 'drawing_id');
		
		return $columns;
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'alias',
			'description',
		);
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeMaterialGroup($material_group_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('materialGroupToMaterial.material_group_id', $material_group_id);

		// join
		$criteria->join = '
			JOIN tbl_material_group_to_material materialGroupToMaterial ON materialGroupToMaterial.material_id = t.id
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>