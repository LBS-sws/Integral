<?php
class RptCreditsList extends CReport {
	protected function fields() {
		return array(
			'activity_name'=>array('label'=>Yii::t('integral','Credit activities Name'),'width'=>22,'align'=>'L'),
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),
			'set_name'=>array('label'=>Yii::t('integral','Integral Name'),'width'=>30,'align'=>'L'),
			'integral'=>array('label'=>Yii::t('integral','Integral Num'),'width'=>25,'align'=>'C'),
			's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),
			'integral_type'=>array('label'=>Yii::t('integral','integral type'),'width'=>20,'align'=>'L'),
			'lcd'=>array('label'=>Yii::t('integral','apply for time'),'width'=>15,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('integral','Credit activities Name').':'.ActivityAddForm::getActivityNameToId($this->criteria['ACTIVITY']).' / '
			.Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$activity = $this->criteria['ACTIVITY'];
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
		
		$citymodel = new City();
		$citylist = $citymodel->getDescendantList($city);
		$citylist = empty($citylist) ? "'$city'" : "$citylist,'$city'";
		
		$suffix = Yii::app()->params['envSuffix'];
		
		$cond_staff = '';
		if (!empty($staff_id)) {
			$ids = explode('~',$staff_id);
			if(count($ids)>1){
                $cond_staff = implode(",",$ids);
            }else{
                $cond_staff = $staff_id;
            }
			if ($cond_staff!=''){
                $cond_staff = " and a.employee_id in ($cond_staff) ";
            } 
		}
        $sql = "select a.*,b.name AS activity_name,d.name AS employee_name,d.city AS s_city,e.integral_name ,e.integral_type 
                from gr_gral_add a 
                LEFT JOIN gr_act_add b ON a.activity_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                LEFT JOIN gr_integral_add e ON a.set_id = e.id
                where d.city in($citylist) and a.state=3 and a.activity_id = '$activity' 
                $cond_staff
				order by d.city desc, a.id desc
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['activity_name'] = $row['activity_name'];
				$temp['employee_name'] = $row['employee_name'];
				$temp['set_name'] = $row['integral_name'];
				$temp['integral'] = $row['integral'];
				$temp['s_city'] = CGeneral::getCityName($row['s_city']);
                $temp['integral_type'] = $row['integral_type'];
                $temp['lcd'] = CGeneral::toDate($row['lcd']);
				$this->data[] = $temp;
			}
		}
		return true;
	}
	
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>