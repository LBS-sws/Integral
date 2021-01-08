<?php
/* Reimbursement Form */

class ReportY06Form extends CReportForm
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
            array('city,city_desc,year,staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
				'CITY'=>$this->city,
				'YEAR'=>$this->year,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
            'city_allow'=>$this->city_allow,
            'CITYDESC'=>$this->city_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptGiftList';
		$this->name = Yii::t('app','Sum Gift Report');
		$this->format = 'EXCEL';
		$this->fields = 'city,year,staffs,staffs_desc';
		$this->year = date("Y");
        $this->city = '';
        $this->city_desc = Yii::t('misc','All');
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
	}

    public function getYearList(){
        $arr=array();
        for ($i=2015;$i<=2025;$i++){
            $arr[$i] = $i.Yii::t("integral","year");
        }
        return $arr;
    }
}
