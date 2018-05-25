<tr class='clickable-row' data-href='<?php echo $this->getLink('SS03', 'activityCut/edit', 'activityCut/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS03', 'activityCut/edit', 'activityCut/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
</tr>
