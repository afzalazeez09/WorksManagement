<?php

/**
 * This is the model class for table "tbl_sub_report".
 *
 * The followings are the available columns in table 'tbl_sub_report':
 * @property string $id
 * @property string $description
 * @property string $select
 * @property string $report_id
 * @property string $format
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Report $report
 * @property User $updatedBy
 */
class SubReport extends ActiveRecord
{
	/**
	 * Data types. These are the emum values set by the format custom type within 
	 * the database
	 */
	const subReportFormatPaged = 'Paged';
	const subReportFormatNotPaged = 'Not paged';
	const subReportFormatNoFormat= 'No format';

	/**
	 * @return array duty level value => duty level display name
	 */
	public static function getFormats()
	{
		return array(
			self::subReportFormatPaged=>self::subReportFormatPaged,
			self::subReportFormatNotPaged=>self::subReportFormatNotPaged,
			self::subReportFormatNoFormat=>self::subReportFormatNoFormat,
		);
	}

	public function scopeSubReportReport_id($report_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('report_id',$report_id);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, select, report_id, format', 'required'),
			array('description', 'length', 'max'=>255),
			array('report_id', 'length', 'max'=>10),
			array('format', 'length', 'max'=>9),
			array('select', 'validationSQL'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, select, report_id, format', 'safe', 'on'=>'search'),
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
            'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'select' => 'Select',
			'report_id' => 'Report',
			'format' => 'Format',
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
			't.format',
			't.select',
		);

		// where
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.format', $this->format, true);
		$criteria->compare('t.select', $this->select, true);
		$criteria->compare('t.report_id', $this->report_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'format';
		$columns[] = 'select';
		
		return $columns;
	}

}