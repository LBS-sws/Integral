<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('DE03', 'cut/edit', 'cut/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('DE03', 'cut/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['activity_name']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['integral_name']; ?></td>
    <td><?php echo $this->record['integral']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
