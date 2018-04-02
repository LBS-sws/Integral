<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('DE02', 'integral/edit', 'integral/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('DE02', 'integral/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['integral_name']; ?></td>
    <td><?php echo $this->record['integral']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
