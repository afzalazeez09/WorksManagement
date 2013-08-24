<?php

/**
 * This is the model class for table "v_drawing_to_assembly".
 *
 * The followings are the available columns in table 'v_drawing_to_assembly':
 * @property integer $id
 * @property integer $drawing_id
 * @property string $description
 */
class DrawingToAssembly extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_drawing_to_assembly';
	}

	static function primaryKeyName() {
		return 'id';
	}

	public function getAdminColumns()
	{
		$columns[] = self::linkColumn('description', 'Assembly', 'id');
		
		return $columns;
	}

}