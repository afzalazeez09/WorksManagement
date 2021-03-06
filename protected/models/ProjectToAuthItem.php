<?php

/**
 * This is the model class for table "tbl_project_to_auth_item".
 *
 * The followings are the available columns in table 'tbl_project_to_auth_item':
 * @property string $id
 * @property string $project_id
 * @property string $auth_item_name
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property AuthItem $authItemName
 * @property User $updatedBy
 * @property ProjectToAuthItemToAuthAssignment[] $projectToAuthItemToAuthAssignments
 */
class ProjectToAuthItem extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'projectToAuthItemToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthItemToAuthAssignment', 'project_to_auth_item_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return array(
			'auth_item_name' => 'Role',
		);
	}

	public function getAdminColumns()
	{
		$columns[] = 'auth_item_name';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='auth_item_name';

		return $displaAttr;
	}

}