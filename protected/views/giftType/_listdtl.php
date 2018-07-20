<tr class='clickable-row' data-href='<?php echo $this->getLink('SS04', 'giftType/edit', 'giftType/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS04', 'giftType/edit', 'giftType/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['gift_name']; ?></td>
	<td><?php echo $this->record['bonus_point']; ?></td>
	<td><?php echo $this->record['inventory']; ?></td>
</tr>
