<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('gift_name').$this->drawOrderArrow('gift_name'),'#',$this->createOrderLink('giftType-list','gift_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('giftType-list','city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bonus_point').$this->drawOrderArrow('bonus_point'),'#',$this->createOrderLink('giftType-list','bonus_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('inventory').$this->drawOrderArrow('inventory'),'#',$this->createOrderLink('giftType-list','inventory'))
        ;
        ?>
    </th>
</tr>
