<?php
/* Reimbursement Form */

class ReportY05Form extends CReportForm
{
	public $staffs;
	public $staffs_desc;
    public $city_desc;
    public $city_allow;
	
	protected function labelsEx() {
		return array(
				'staffs'=>Yii::t('integral','Staffs'),
			);
	}
	
	protected function rulesEx() {
        return array(
            array('city,city_desc,staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
                'CITY'=>$this->city,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
            'city_allow'=>$this->city_allow,
            'CITYDESC'=>$this->city_desc,
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
        $this->city = '';
        $this->city_desc = Yii::t('misc','All');
	}

}
