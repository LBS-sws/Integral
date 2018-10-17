<tr class=''>

    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <?php
    foreach ($this->model->prize_list as $item){
        echo "<td>";
        echo $this->record['prize_'.$item["id"]];
        echo "</td>";
    }
    ?>
    <td><?php echo $this->record['prize_sum']; ?></td>
</tr>
