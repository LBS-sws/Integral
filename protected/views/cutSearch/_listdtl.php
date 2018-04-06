<tr class='clickable-row' data-href='<?php echo $this->getLink('SR03', 'cutSearch/view', 'cutSearch/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->needHrefButton('SR03', 'cutSearch/view', 'view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['integral_name']; ?></td>
    <td><?php echo $this->record['integral']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
