<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('d.code'),'#',$this->createOrderLink('giftSearch-list','d.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('giftSearch-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('giftSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('gift_name').$this->drawOrderArrow('a.gift_type'),'#',$this->createOrderLink('giftSearch-list','a.gift_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bonus_point').$this->drawOrderArrow('a.bonus_point'),'#',$this->createOrderLink('giftSearch-list','a.bonus_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_num').$this->drawOrderArrow('a.apply_num'),'#',$this->createOrderLink('giftSearch-list','a.apply_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('giftSearch-list','a.apply_date'))
        ;
        ?>
    </th>
</tr>
