<?php
/* Reimbursement Form */

class ReportY04Form extends CReportForm
{
	public $activity;
	public $staffs;
	public $staffs_desc;
	
	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('integral','Staffs'),
				'activity'=>Yii::t('integral','Cut activities Name'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('city,staffs,activity, staffs_desc','safe'),
            array('activity','required'),
        );
	}
	
	protected function queueItemEx() {
		return array(
                'CITY'=>$this->city,
				'ACTIVITY'=>$this->activity,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptCutList';
		$this->name = Yii::t('app','Cut subsidiary List');
		$this->format = 'EXCEL';
		$this->fields = 'city,activity,staffs,staffs_desc';
		$this->activity = '';
        $this->city = Yii::app()->user->city();
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
	}

}
