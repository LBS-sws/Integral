<tr class='clickable-row' data-href='<?php echo $this->getLink('SS02', 'activityAdd/edit', 'activityAdd/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS02', 'activityAdd/edit', 'activityAdd/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
</tr>
