<tr>
    <th><input name="Fruit"  type="checkbox"  id="all"></th>
    <th>&nbsp;</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('confirmCredit-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('confirmCredit-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_name').$this->drawOrderArrow('a.credit_type'),'#',$this->createOrderLink('confirmCredit-list','a.credit_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_point').$this->drawOrderArrow('a.credit_point'),'#',$this->createOrderLink('confirmCredit-list','a.credit_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('category').$this->drawOrderArrow('b.category'),'#',$this->createOrderLink('confirmCredit-list','b.category'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('confirmCredit-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('confirmCredit-list','a.state'))
        ;
        ?>
    </th>
</tr>
