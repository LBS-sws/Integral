<tr class='clickable-row' data-href='<?php echo $this->getLink('SS01', 'creditType/edit', 'creditType/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS01', 'creditType/edit', 'creditType/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['credit_code']; ?></td>
	<td><?php echo $this->record['credit_name']; ?></td>
	<td><?php echo $this->record['credit_point']; ?></td>
	<td><?php echo $this->record['category']; ?></td>
	<td><?php echo $this->record['validity']; ?></td>
	<td><?php echo $this->record['rule']; ?></td>
	<td><?php echo $this->record['display']; ?></td>
</tr>
