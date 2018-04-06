<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('sumSearch-list','a.employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('sumSearch-list','d.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sum_integral').$this->drawOrderArrow('sum_integral'),'#',$this->createOrderLink('sumSearch-list','sum_integral'))
			;
		?>
	</th>
</tr>
