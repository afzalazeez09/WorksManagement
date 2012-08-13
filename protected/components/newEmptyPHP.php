<?php
/*
 * Autocomplete using a foreign key field
 */
class WMEJuiAutoCompleteFkField extends WMEJuiAutoCompleteField
{

	/**
	 * @var string the foreign key field.
	 */
	public $fkField;
	/**
	 * @var string the relation name to the FK table
	 */
	public $relName;

    public function init()
    {
 		$relations = $this->model->relations();
		$fKModelType = $relations[$this->relName][1];
		$this->attribute = $relations[$this->relName][2];	//the FK field (from CJuiInputWidget)
        $this->_fieldID = $this->fkField;
        $this->_saveID = "{$this->fkField}_save";
        $this->_lookupID = "{$this->fkField}_lookup";
		
		foreach($fKModelType::getDisplayAttr() as $key => $field)
		{
			$field = str_replace('.', '->', $field);
			if(is_numeric($key))
			{
				eval('$this->_display[] = $this->model->{$this->relName}->'."$field;");
			}
			else
			{
				$key = str_replace('.', '->', $key);
				eval('$this->_display[] = $this->model->{$this->relName}->'."$key->$field;");
			}
		}
		
		$this->sourceUrl = Yii::app()->createUrl("$fKModelType/autocomplete", array('fk_model' => $fKModelType )); 
		$this->_display = !empty($this->model->{$this->attribute})
			? implode(Yii::app()->params['delimiter']['display'], $this->_display)
			: '';

		parent::init(); // ensure necessary assets are loaded

		echo $this->form->labelEx($this->model, $this->fkField);
	}
	
    public function run()
    {
 
        parent::run();
		
		echo $this->form->error($this->model, $this->fkField);
    }
}

?>
