<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'creditflowinfodialog',
					'header'=>Yii::t('dialog','Flow Info'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="box" id="flow-boss-list" style="max-height: 300px; overflow-y: auto;">
	<table id="tblBossFlow" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
                <th width="16%"><?php echo Yii::t("integral","Operator User"); ?></th>
                <th width="28%"><?php echo Yii::t("integral","Operator Time"); ?></th>
                <th width="18%"><?php echo Yii::t("integral","Operation"); ?></th>
                <th><?php echo Yii::t("integral","Remark"); ?></th>
			</tr>
		</thead>
		<tbody>

        <?php
            if(!empty($flowList)){
                foreach ($flowList as $item){
                    echo "<tr>";
                    echo "<td>".$item["disp_name"]."</td>";
                    echo "<td>".$item["lcd"]."</td>";
                    echo "<td>".Yii::t("integral",$item["state_type"])."</td>";
                    echo "<td>".$item["state_remark"]."</td>";
                    echo "</tr>";
                }
            }
        ?>
		</tbody>
	</table>
</div>

<?php
	$this->endWidget();
?>
