<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_code').$this->drawOrderArrow('credit_code'),'#',$this->createOrderLink('creditType-list','credit_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_name').$this->drawOrderArrow('credit_name'),'#',$this->createOrderLink('creditType-list','credit_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_point').$this->drawOrderArrow('credit_point'),'#',$this->createOrderLink('creditType-list','credit_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('category').$this->drawOrderArrow('category'),'#',$this->createOrderLink('creditType-list','category'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('validity').$this->drawOrderArrow('validity'),'#',$this->createOrderLink('creditType-list','validity'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('rule').$this->drawOrderArrow('rule'),'#',$this->createOrderLink('creditType-list','rule'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('display').$this->drawOrderArrow('display'),'#',$this->createOrderLink('creditType-list','display'))
        ;
        ?>
    </th>
</tr>
