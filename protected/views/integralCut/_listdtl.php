<tr class='clickable-row' data-href='<?php echo $this->getLink('SS04', 'integralCut/edit', 'integralCut/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS04', 'integralCut/edit', 'integralCut/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['integral_name']; ?></td>
	<td><?php echo $this->record['integral_num']; ?></td>
	<td><?php echo $this->record['inventory']; ?></td>
</tr>
