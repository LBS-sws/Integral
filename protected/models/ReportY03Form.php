<?php
/* Reimbursement Form */

class ReportY03Form extends CReportForm
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
            array('city,year,staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
				'CITY'=>$this->city,
				'YEAR'=>$this->year,
				'STAFFS'=>$this->staffs,
				'STAFFSDESC'=>$this->staffs_desc,
			);
	}
	
	public function init() {
		$this->id = 'RptYearList';
		$this->name = Yii::t('app','Credits year List');
		$this->format = 'EXCEL';
		$this->fields = 'city,year,staffs,staffs_desc';
		$this->year = date("Y");
        $this->city = Yii::app()->user->city();
		$this->staffs = '';
		$this->staffs_desc = Yii::t('misc','All');
	}

    public function getYearList(){
        $sql = "select year from gr_credit_point_ex GROUP BY year ORDER by year asc";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        $arr=array(''=>"所有");
        foreach ($rows as $row){
            $arr[$row["year"]] = $row["year"].Yii::t("integral","year");
        }
        return $arr;
    }
}
