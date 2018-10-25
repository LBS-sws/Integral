<?php
/* Reimbursement Form */

class ReportY05Form extends CReportForm
{
	public $staffs;
	public $staffs_desc;
	
	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('integral','Staffs'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('city,staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
                'CITY'=>$this->city,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptStretchList';
		$this->name = Yii::t('app','Stretch List Report');
		$this->format = 'EXCEL';
		$this->fields = 'city,,staffs,staffs_desc';
        $this->city = Yii::app()->user->city();
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
	}

}
