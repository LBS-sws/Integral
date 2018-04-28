
<?php
 echo '<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" name="'.$name.'">';
?>

<?php
	$ftrbtn = array();
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>"importUp",'submit'=>Yii::app()->createUrl('integralSearch/importIntegral')));
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importIntegral',
					'header'=>Yii::t('integral','Import File'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo Yii::t("integral","file");?></label>
    <div class="col-sm-6">
        <?php echo TbHtml::fileField($name.'[file]',"",array("class"=>"form-control")); ?>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-10 col-sm-offset-1">
        <p class="form-control-static text-warning"><?php echo Yii::t("integral","The employee can only import once every year. If there are multiple data, please combine the credits. (prevent repeated imports)");?></p>
    </div>
</div>

<?php
	$this->endWidget(); 
?>
<?php
echo '</form>';
?>
