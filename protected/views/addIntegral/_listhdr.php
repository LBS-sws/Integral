<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('addIntegral-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('addIntegral-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('addIntegral-list','end_time'))
        ;
        ?>
    </th>
    <th>
    </th>
</tr>
