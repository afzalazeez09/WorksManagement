<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('travel_time_1_way')); ?>:</b>
	<?php echo CHtml::encode($data->travel_time_1_way); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('critical_completion')); ?>:</b>
	<?php echo CHtml::encode($data->critical_completion); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('planned')); ?>:</b>
	<?php echo CHtml::encode($data->planned); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->project_type_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('staff_id')); ?>:</b>
	<?php echo CHtml::encode($data->staff_id); ?>
	<br />


</div>