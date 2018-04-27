<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral_name').$this->drawOrderArrow('integral_name'),'#',$this->createOrderLink('integralAdd-list','integral_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral_num').$this->drawOrderArrow('integral_num'),'#',$this->createOrderLink('integralAdd-list','integral_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral_type').$this->drawOrderArrow('integral_type'),'#',$this->createOrderLink('integralAdd-list','integral_type'))
        ;
        ?>
    </th>
</tr>
