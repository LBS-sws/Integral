<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'gift-list',
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
            <?php
            $listArrIntegral = GiftList::getNowIntegral();
            echo  '<div class="btn-group pull-left text-right"><span class="text-success">'.date("Y")."年".Yii::t('integral','Sum Gift')."：".$listArrIntegral["sum"]."</span></div>";
            echo  '<div class="btn-group pull-right text-right"><span class="text-success">'.date("Y")."年".Yii::t('integral','Available Gift')."：".$listArrIntegral["cut"]."</span></div>";

            ?>
        </div>
    </div>
    <?php $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('integral','Integral Cut List'),
        'model'=>$model,
        'viewhdr'=>'//gift/_listhdr',
        'viewdtl'=>'//gift/_listdtl',
        'search'=>array(
            'gift_name',
            'bonus_point',
            'city_name',
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
        'submit'=> Yii::app()->createUrl('gift/apply'),
    ));
    ?>
</form>
<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

