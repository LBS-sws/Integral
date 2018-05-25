
<?php if ($this->record['status']['bool']): ?>
<tr class='clickable-row <?php echo $this->record['status']['color']; ?>' data-href='<?php echo Yii::app()->createUrl('addIntegral/new',array("index"=>$this->record['id']));?>'>
    <?php else: ?>
<tr class='<?php echo $this->record['status']['color']; ?>' data-href=''>
    <?php endif ?>

    <td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
    <?php if ($this->record['status']['bool']): ?>
        <td><?php echo TbHtml::link('<span class="glyphicon glyphicon-plus"></span> ', Yii::app()->createUrl('addIntegral/new',array("index"=>$this->record['id']))); ?></td>
    <?php else: ?>
        <td><?php echo $this->record['status']['str']; ?></td>
    <?php endif ?>
</tr>
