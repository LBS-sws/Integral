<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_name').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('auditAdd-list','e.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('auditAdd-list','a.employee_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('auditAdd-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('set_id').$this->drawOrderArrow('a.set_id'),'#',$this->createOrderLink('auditAdd-list','a.set_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('integral').$this->drawOrderArrow('a.integral'),'#',$this->createOrderLink('auditAdd-list','a.integral'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('auditAdd-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('auditAdd-list','a.state'))
        ;
        ?>
    </th>
</tr>
