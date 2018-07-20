<tr class='clickable-row' data-href='<?php echo $this->getLink('SR01', 'creditSearch/view', 'creditSearch/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->needHrefButton('SR01', 'creditSearch/view', 'view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['credit_name']; ?></td>
    <td><?php echo $this->record['credit_point']; ?></td>
    <td><?php echo $this->record['category']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
</tr>
