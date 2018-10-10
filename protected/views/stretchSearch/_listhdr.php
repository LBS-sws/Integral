<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('stretchSearch-list','a.employee_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('stretchSearch-list','d.city'))
        ;
        ?>
    </th>
    <?php
        foreach ($this->model->prize_list as $item){
            echo "<th>";
            echo TbHtml::link($item["prize_name"].$this->drawOrderArrow('prize_'.$item["id"]),'#',$this->createOrderLink('stretchSearch-list','prize_'.$item["id"]))
            ;
            echo "</th>";
        }
    ?>
    <th>
        <?php echo TbHtml::link(Yii::t("integral","Three consecutive championships"),'#')
        ;
        ?>
    </th>
</tr>
