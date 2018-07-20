<tr class='clickable-row' data-href='<?php echo $this->getLink('SR03', 'giftSearch/view', 'giftSearch/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('SR03', 'giftSearch/view', 'view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['gift_name']; ?></td>
    <td><?php echo $this->record['bonus_point']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
</tr>
