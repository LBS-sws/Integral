<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('a.employee_code'),'#',$this->createOrderLink('sumSearch-list','a.employee_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('a.employee_name'),'#',$this->createOrderLink('sumSearch-list','a.employee_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('sumSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.year'),'#',$this->createOrderLink('sumSearch-list','a.year'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_num').$this->drawOrderArrow('start_num'),'#',$this->createOrderLink('sumSearch-list','start_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_num').$this->drawOrderArrow('end_num'),'#',$this->createOrderLink('sumSearch-list','end_num'))
        ;
        ?>
    </th>
</tr>
