<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'integralCut-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
        <strong><?php echo Yii::t('app','Credits for'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('cutIntegral/index')));
                ?>
            </div>
            <div class="btn-group pull-right text-right" role="group">
                <?php
                $listArrIntegral = IntegralCutView::getNowIntegral();
                echo  '<span class="text-success">'.date("Y")."年".Yii::t('integral','Sum Integral')."：".$listArrIntegral["sum"]."</span><br>";
                echo  '<span class="text-success">'.date("Y")."年".Yii::t('integral','Available integral')."：".$listArrIntegral["cut"]."</span>";

                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('integral','Integral Cut List'),
        'model'=>$model,
        'viewhdr'=>'//cutIntegral/_listhdr_cut',
        'viewdtl'=>'//cutIntegral/_listdtl_cut',
        'searchlinkparam'=>array('index'=>$index),
        'search'=>array(
            'integral_name',
            'integral_num',
        ),
    ));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>


<form class="form-horizontal MultiFile-intercepted" action="" method="post">
    <?php $this->renderPartial('//site/integralApply',array(
        'submit'=> Yii::app()->createUrl('cutIntegral/apply'),
        'activity_id'=>$index,
    ));
    ?>
</form>
<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

