<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('d.code'),'#',$this->createOrderLink('deductionSearch-list','d.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('deductionSearch-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('deductionSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('category').$this->drawOrderArrow('f.category'),'#',$this->createOrderLink('deductionSearch-list','f.category'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_name').$this->drawOrderArrow('f.credit_name'),'#',$this->createOrderLink('deductionSearch-list','f.credit_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_num').$this->drawOrderArrow('start_num'),'#',$this->createOrderLink('deductionSearch-list','start_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_num').$this->drawOrderArrow('end_num'),'#',$this->createOrderLink('deductionSearch-list','end_num'))
        ;
        ?>
    </th>
</tr>
