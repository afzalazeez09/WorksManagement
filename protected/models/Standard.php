<?php

/**
 * This is the model class for table "tbl_standard".
 *
 * The followings are the available columns in table 'tbl_standard':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly[] $assemblies
 * @property AssemblyGroup[] $assemblyGroups
 * @property AssemblyToDrawing[] $assemblyToDrawings
 * @property Drawing[] $drawings
 * @property Material[] $materials
 * @property MaterialGroup[] $materialGroups
 * @property User $updatedBy
 */
class Standard extends ActiveRecord
{
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
            'assemblies' => array(self::HAS_MANY, 'Assembly', 'standard_id'),
            'assemblyGroups' => array(self::HAS_MANY, 'AssemblyGroup', 'standard_id'),
            'assemblyToDrawings' => array(self::HAS_MANY, 'AssemblyToDrawing', 'standard_id'),
            'drawings' => array(self::HAS_MANY, 'Drawing', 'standard_id'),
            'materials' => array(self::HAS_MANY, 'Material', 'standard_id'),
            'materialGroups' => array(self::HAS_MANY, 'MaterialGroup', 'standard_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);

		$criteria->select=array(
			't.id',
			't.name',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('name');
		
		return $columns;
	}

}

?>