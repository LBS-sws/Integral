<?php
class RptCutList extends CReport {
	protected function fields() {
		return array(
			'activity_name'=>array('label'=>Yii::t('integral','Cut activities Name'),'width'=>22,'align'=>'L'),
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),
            's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),
			'set_name'=>array('label'=>Yii::t('integral','Cut Name'),'width'=>30,'align'=>'L'),
			'integral'=>array('label'=>Yii::t('integral','Cut Integral'),'width'=>25,'align'=>'C'),
            'apply_num'=>array('label'=>Yii::t('integral','Number of applications'),'width'=>20,'align'=>'L'),
            'integral_sum'=>array('label'=>Yii::t('integral','Cut Integral Sum'),'width'=>20,'align'=>'L'),
			'lcd'=>array('label'=>Yii::t('integral','apply for time'),'width'=>15,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('integral','Cut activities Name').':'.ActivityCutForm::getActivityCutNameToId($this->criteria['ACTIVITY']).' / '
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
        $sql = "select a.*,b.name AS activity_name,d.name AS employee_name,d.city AS s_city,e.integral_name 
                from gr_gral_cut a 
                LEFT JOIN gr_act_cut b ON a.activity_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                LEFT JOIN gr_integral_cut e ON a.set_id = e.id
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
                $temp['apply_num'] = $row['apply_num'];
                $temp['integral_sum'] = intval($row['apply_num'])*intval($row['integral']);
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