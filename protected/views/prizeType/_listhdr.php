<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_name').$this->drawOrderArrow('prize_name'),'#',$this->createOrderLink('prizeType-list','prize_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_point').$this->drawOrderArrow('prize_point'),'#',$this->createOrderLink('prizeType-list','prize_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('min_point').$this->drawOrderArrow('min_point'),'#',$this->createOrderLink('prizeType-list','min_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('tries_limit').$this->drawOrderArrow('tries_limit'),'#',$this->createOrderLink('prizeType-list','tries_limit'))
        ;
        ?>
    </th>
</tr>
