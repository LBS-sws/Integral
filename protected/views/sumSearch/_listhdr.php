<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('sumSearch-list','a.employee_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('sumSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral').$this->drawOrderArrow('num'),'#',$this->createOrderLink('sumSearch-list','num'))
        ;
        ?>
    </th>
</tr>
