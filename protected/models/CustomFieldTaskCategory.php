<?php

/**
 * This is the model class for table "tbl_custom_field_task_category".
 *
 * The followings are the available columns in table 'tbl_custom_field_task_category':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $task_template_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskTemplate $taskTemplate
 * @property TaskTemplateToCustomField[] $taskTemplateToCustomFields
 * @property TaskTemplateToCustomField[] $taskTemplateToCustomFields1
 */
class CustomFieldTaskCategory extends CategoryActiveRecord {
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Field set';

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'taskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskTemplateToCustomField', 'task_template_id'),
            'taskTemplateToCustomFields1' => array(self::HAS_MANY, 'TaskTemplateToCustomField', 'custom_field_task_category_id'),
        );
    }

}