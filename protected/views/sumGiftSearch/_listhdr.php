<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('d.code'),'#',$this->createOrderLink('sumGiftSearch-list','d.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('sumGiftSearch-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('sumGiftSearch-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('d.id'),'#',$this->createOrderLink('sumGiftSearch-list','d.id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('sum_gift').$this->drawOrderArrow('a.sum_gift'),'#',$this->createOrderLink('sumGiftSearch-list','a.sum_gift'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('sum_apply').$this->drawOrderArrow('b.sum_apply'),'#',$this->createOrderLink('sumGiftSearch-list','b.sum_apply'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('num').$this->drawOrderArrow('num'),'#',$this->createOrderLink('sumGiftSearch-list','num'))
        ;
        ?>
    </th>
</tr>
