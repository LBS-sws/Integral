<?php
/* Reimbursement Form */

class ReportY04Form extends CReportForm
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
            array('city,city_desc,staffs,start_dt,end_dt, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
                'CITY'=>$this->city,
                'CITYDESC'=>$this->city_desc,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
				'city_allow'=>$this->city_allow,
			);
	}
	
	public function init() {
		$this->id = 'RptCutList';
		$this->name = Yii::t('app','Cut subsidiary List');
		$this->format = 'EXCEL';
		$this->fields = 'city,start_dt,end_dt,staffs,staffs_desc';
        $this->start_dt = date("Y/01/01");
        $this->end_dt = date("Y/12/31");
        $this->city = '';
        $this->city_desc = Yii::t('misc','All');
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
	}

}
