<?php

/**
 * This is the model class for table "tbl_task_template_to_assembly".
 *
 * The followings are the available columns in table 'tbl_task_template_to_assembly':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $assembly_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property Assembly $assembly
 * @property User $updatedBy
 */
class TaskTemplateToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;
	public $searchAlias;

	public $standard_id;
	public $clientAlias;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('standard_id', 'required'),
			array('standard_id', 'numerical', 'integerOnly'=>true),
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
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAssembly', $this->searchAssembly, 'assembly.description', true);
		$criteria->composite('searchAlias', $this->searchAlias, array(
			'assembly.alias',
			'clientToAssembly.alias'
		));

		// join
		$criteria->join = '
			LEFT JOIN tbl_task_template taskTemplate ON t.task_template_id = taskTemplate.id
			LEFT JOIN tbl_client_to_assembly clientToAssembly ON t.assembly_id = clientToAssembly.assembly_id
				AND taskTemplate.client_id = clientToAssembly.client_id
		';

		// with
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchAssembly';
 		$columns[] = 'searchAlias';
 		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'quantity_tooltip';
 		$columns[] = 'select';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
//			'taskTemplate->description',
			'searchAssembly',
//			'assembly->unit',
			'searchAlias',
		);
	}

	public function afterFind() {
		$this->standard_id = $this->assembly->standard_id;
		
		if($clientToAssembly = ClientToAssembly::model()->findByAttributes(array(
			'assembly_id'=>$this->assembly_id,
			'client_id'=>$this->taskTemplate->client_id,
		)))
		{
			$this->clientAlias = $clientToAssembly->alias;
		}
		
		return parent::afterFind();
	}
	
}