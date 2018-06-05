<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('activity_name').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('cutSearch-list','e.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('cutSearch-list','a.employee_id'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('cutSearch-list','d.city'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('set_id').$this->drawOrderArrow('a.set_id'),'#',$this->createOrderLink('cutSearch-list','a.set_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('integral').$this->drawOrderArrow('a.integral'),'#',$this->createOrderLink('cutSearch-list','a.integral'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_num').$this->drawOrderArrow('a.apply_num'),'#',$this->createOrderLink('cutSearch-list','a.apply_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('cutSearch-list','a.lcd'))
			;
		?>
	</th>
</tr>
