<?php

/**
 * This is the model class for table "tbl_project_template".
 *
 * The followings are the available columns in table 'tbl_project_template':
 * @property integer $id
 * @property string $description
 * @property integer $client_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Action[] $actions
 * @property CustomFieldProjectCategory[] $customFieldProjectCategories
 * @property CustomFieldToProjectTemplate[] $customFieldToProjectTemplates
 * @property User $updatedBy
 * @property Client $client
 * @property ProjectTemplateToAuthItem[] $projectTemplateToAuthItems
 * @property ProjectType[] $projectTypes
 * @property ProjectType[] $projectTypes1
 * @property TaskTemplate[] $taskTemplates
 * @property TaskTemplate[] $taskTemplates1
 */
class ProjectTemplate extends ActiveRecord
{

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, client_id', 'required'),
			array('client_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, description, client_id, searchClient', 'safe', 'on'=>'search'),
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
            'actions' => array(self::HAS_MANY, 'Action', 'project_template_id'),
            'customFieldProjectCategories' => array(self::HAS_MANY, 'CustomFieldProjectCategory', 'project_template_id'),
            'customFieldToProjectTemplates' => array(self::HAS_MANY, 'CustomFieldToProjectTemplate', 'project_template_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'projectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectTemplateToAuthItem', 'project_template_id'),
            'projectTypes' => array(self::HAS_MANY, 'ProjectType', 'project_template_id'),
            'projectTypes1' => array(self::HAS_MANY, 'ProjectType', 'client_id'),
            'taskTemplates' => array(self::HAS_MANY, 'TaskTemplate', 'project_template_id'),
            'taskTemplates1' => array(self::HAS_MANY, 'TaskTemplate', 'client_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'client_id' => 'Client',
			'searchClient' => 'Client',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.description',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.client_id', $this->client_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		
		return $columns;
	}

	public function scopeClient($client_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('client_id', $client_id);

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}
}

?>