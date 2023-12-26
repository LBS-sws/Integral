
<?php
	$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
$ftrbtn[] = TbHtml::button(Yii::t('integral','apply'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>$submit));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'integralApply',
					'header'=>Yii::t('integral','apply cut'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('integral','Cut Name'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::hiddenField('GiftRequestForm[gift_type]','',
                array('id'=>'gift_type')
            ); ?>
            <?php echo TbHtml::textField('GiftRequestForm[gift_name]','',
                array('readonly'=>(true),'id'=>'gift_name')
            ); ?>
        </div>
        <?php echo TbHtml::label(Yii::t('integral','Cut Integral'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::numberField('GiftRequestForm[bonus_point]','',
                array('readonly'=>(true),'id'=>'bonus_point')
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('integral','Number of applications'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::numberField('GiftRequestForm[apply_num]','1',
                array('readonly'=>(false),'id'=>'apply_num')
            ); ?>
        </div>
        <?php echo TbHtml::label(Yii::t('integral','Points Integral'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::numberField('GiftRequestForm[point_gift]','',
                array('readonly'=>(true),'id'=>'point_gift')
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('integral','Remark'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-8">
            <?php echo TbHtml::textArea('GiftRequestForm[remark]','',
                array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>(false))
            ); ?>
        </div>
    </div>
<?php
	$this->endWidget();
?>

<?php

$js = "
$('#apply_num').change(function(){
    var num = $('#apply_num').val();
    var gift = $('#bonus_point').val();
    if(!isNaN(num)&&!isNaN(gift)){
        var point_gift = num*gift;
        $('#point_gift').val(point_gift);
    }
});
";
Yii::app()->clientScript->registerScript('pointGift',$js,CClientScript::POS_READY);
?>
