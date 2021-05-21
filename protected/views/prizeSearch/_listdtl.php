<tr class='clickable-row ' data-href='<?php echo $this->getLink('SR07', 'prizeSearch/edit', 'prizeSearch/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('DE03', 'prizeSearch/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_name']; ?></td>
    <td><?php echo $this->record['prize_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
</tr>
