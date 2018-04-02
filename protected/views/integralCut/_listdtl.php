<tr class='clickable-row' data-href='<?php echo $this->getLink('EX01', 'integralCut/edit', 'integralCut/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('EX01', 'integralCut/edit', 'integralCut/view', array('index'=>$this->record['id'])); ?></td>
	<td class="integral_name"><?php echo $this->record['integral_name']; ?></td>
	<td class="integral_num"><?php echo $this->record['integral_num']; ?></td>
	<td><?php echo $this->record['inventory']; ?></td>
	<td>
        <?php
/*        echo TbHtml::button('<span class="fa fa-cube"></span> '.Yii::t('app','Exchange'), array(
            'submit'=>Yii::app()->createUrl('integralCut/apply'),
        ));*/
        echo TbHtml::button('<span class="fa  fa-cube"></span> '.Yii::t('app','Exchange'), array(
                'class'=>'btnIntegralApply','data-id'=>$this->record['id'])
        );
        ?>
    </td>
</tr>

<script>
    $(function () {
        $(".btnIntegralApply").on("click",function () {
            var $tr = $(this).parents("tr:first");
            $("#set_id").val($(this).data("id"));
            $("#integral_name").val($tr.find(".integral_name:first").text());
            $("#integral").val($tr.find(".integral_num:first").text());
            $('#integralApply').modal('show');
            return false;
        })
    })
</script>