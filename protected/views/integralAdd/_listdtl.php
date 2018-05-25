<tr class='clickable-row' data-href='<?php echo $this->getLink('SS01', 'integralAdd/edit', 'integralAdd/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS01', 'integralAdd/edit', 'integralAdd/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['integral_name']; ?></td>
	<td><?php echo $this->record['integral_num']; ?></td>
	<td><?php echo $this->record['integral_type']; ?></td>
	<td><?php echo $this->record['validity']; ?></td>
</tr>
