<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('creditSearch-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('creditSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_name').$this->drawOrderArrow('a.credit_type'),'#',$this->createOrderLink('creditSearch-list','a.credit_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_point').$this->drawOrderArrow('a.credit_point'),'#',$this->createOrderLink('creditSearch-list','a.credit_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('category').$this->drawOrderArrow('b.category'),'#',$this->createOrderLink('creditSearch-list','b.category'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('creditSearch-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('exp_date').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('creditSearch-list','a.lcd'))
        ;
        ?>
    </th>
</tr>
