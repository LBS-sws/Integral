
<?php if ($this->record['status']['bool']): ?>
<tr class='clickable-row <?php echo $this->record['status']['color']; ?>' data-href='<?php echo Yii::app()->createUrl('cutIntegral/view',array("index"=>$this->record['id']));?>'>
    <?php else: ?>
<tr class='<?php echo $this->record['status']['color']; ?>' data-href=''>
    <?php endif ?>

    <?php if ($this->record['status']['bool']): ?>
        <td><?php echo $this->needHrefButton('EX01', 'cutIntegral/view', 'view', array('index'=>$this->record['id'])); ?></td>
    <?php endif ?>
    <td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['status']['str']; ?></td>
</tr>
