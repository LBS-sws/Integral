<tr class='clickable-row' data-href='<?php echo $this->getLink('SR02', 'sumSearch/view', 'sumSearch/view', array('index'=>$this->record['employee_name']));?>'>
    <td><?php echo $this->needHrefButton('SR02', 'sumSearch/view', 'view', array('index'=>$this->record['employee_name'])); ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['sum_integral']; ?></td>
</tr>
