<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'creditRequest-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Credit application'); ?></strong>
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
    <?php if (Yii::app()->user->validFunction('ZR03')): ?>
    <div class="box">
        <div class="box-body">
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('integral','Import File'), array(
                    'data-toggle'=>'modal','data-target'=>'#importIntegral'));
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php
    $search = array(
        'credit_name',
        'credit_point',
        'employee_name',
        'city_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField($modelName.'[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Apply list'),
        'model'=>$model,
        'viewhdr'=>'//creditRequest/_listhdr',
        'viewdtl'=>'//creditRequest/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>$search,
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

<?php
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if (Yii::app()->user->validFunction('ZR03'))
    $this->renderPartial('//site/importIntegral',array('name'=>"UploadExcelForm","model"=>$model,"submit"=>Yii::app()->createUrl('creditRequest/importCredit')));
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

