<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('EX02', 'giftRequest/edit', 'giftRequest/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('EX02', 'giftRequest/edit', 'edit', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['gift_name']; ?></td>
    <td><?php echo $this->record['bonus_point']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
