<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('cutIntegral-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('cutIntegral-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('cutIntegral-list','end_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link(Yii::t("integral","Status"),"javascript::void(0);");?>
    </th>
</tr>
