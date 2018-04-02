<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral_name').$this->drawOrderArrow('integral_name'),'#',$this->createOrderLink('integralCut-list','integral_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral_num').$this->drawOrderArrow('integral_num'),'#',$this->createOrderLink('integralCut-list','integral_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('inventory').$this->drawOrderArrow('inventory'),'#',$this->createOrderLink('integralCut-list','inventory'))
        ;
        ?>
    </th>
    <th width="5%">
    </th>
</tr>
