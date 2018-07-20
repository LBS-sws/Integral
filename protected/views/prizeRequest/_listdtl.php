<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('DE03', 'prizeRequest/edit', 'prizeRequest/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('DE03', 'prizeRequest/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_name']; ?></td>
    <td><?php echo $this->record['prize_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
