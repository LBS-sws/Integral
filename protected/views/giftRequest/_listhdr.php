<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('a.employee_id'),'#',$this->createOrderLink('giftRequest-list','a.employee_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('gift_name').$this->drawOrderArrow('a.gift_type'),'#',$this->createOrderLink('giftRequest-list','a.gift_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('bonus_point').$this->drawOrderArrow('a.bonus_point'),'#',$this->createOrderLink('giftRequest-list','a.bonus_point'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_num').$this->drawOrderArrow('a.apply_num'),'#',$this->createOrderLink('giftRequest-list','a.apply_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('gift_point').$this->drawOrderArrow('gift_point'),'#',$this->createOrderLink('giftRequest-list','gift_point'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('giftRequest-list','a.apply_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('giftRequest-list','a.state'))
			;
		?>
	</th>
</tr>
