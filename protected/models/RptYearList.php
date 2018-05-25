<?php
class RptYearList extends CReport {
	protected function fields() {
		return array(
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),//員工
			'integral_add'=>array('label'=>Yii::t('integral','Sum Integral'),'width'=>25,'align'=>'C'),//總學分
			'integral_cut'=>array('label'=>Yii::t('integral','Cut Integral'),'width'=>25,'align'=>'C'),//已經使用過的學分
			'integral'=>array('label'=>Yii::t('integral','Available integral'),'width'=>25,'align'=>'C'),//當前可用學分
			's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),//城市
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('app','Credits year List').':'.$this->criteria['YEAR'].' / '
			.Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$year = $this->criteria['YEAR'];
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
		
		$citymodel = new City();
		$citylist = $citymodel->getDescendantList($city);
		$citylist = empty($citylist) ? "'$city'" : "$citylist,'$city'";
		
		$suffix = Yii::app()->params['envSuffix'];

        $yearOld = intval($year)-4;
        $yearOld2 = intval($year)-1;
		$dateSql = " a.lcd>='$year-01-01 00:00:00' and a.lcd<='$year-12-31 23:59:59' ";
		$dateSql2 = " ((a.lcd>='$year-01-01 00:00:00' and a.lcd<='$year-12-31 23:59:59' and e.validity=1)or(a.lcd>='$yearOld-01-01 00:00:00' and a.lcd<='$year-12-31 23:59:59' and e.validity=5)) ";
		$dateSql3 = " (a.lcd>='$yearOld-01-01 00:00:00' and a.lcd<='$yearOld2-12-31 23:59:59' and e.validity=5) ";//5年期限過期的學分
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
        $sql = "select a.employee_id,SUM(a.integral*a.apply_num) AS num
                from gr_gral_cut a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city in($citylist) and a.state IN (1,3) and $dateSql 
                $cond_staff
                GROUP BY a.employee_id
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $arr = array();//扣除積分數組
        foreach ($rows as $row){
            $arr[$row["employee_id"]] = $row["num"];
        }
        $sql = "select a.employee_id,SUM(a.integral) AS num
                from gr_gral_add a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                LEFT JOIN gr_integral_add e ON a.set_id = e.id
                where d.city in($citylist) and a.state=3 and $dateSql3 
                $cond_staff
                GROUP BY a.employee_id
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $arrYear = array();//過期的5年學分
        foreach ($rows as $row){
            $arrYear[$row["employee_id"]] = $row["num"];
        }
        $sql = "select a.employee_id,d.name AS employee_name,d.city AS s_city,SUM(a.integral) AS num
                from gr_gral_add a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                LEFT JOIN gr_integral_add e ON a.set_id = e.id
                where d.city in($citylist) and a.state=3 and $dateSql2 
                $cond_staff
                GROUP BY a.employee_id
                ORDER BY num DESC 
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['employee_name'] = $row['employee_name'];
				$temp['integral_add'] = $row['num'];
				if(key_exists($row["employee_id"],$arr)){
				    $cut_num = intval($arr[$row["employee_id"]]);
				    $integral = intval($row['num'])-intval($arr[$row["employee_id"]]);
                    $temp['integral_cut'] = $cut_num;
                    $temp['integral'] = $integral;
                }else{
                    $temp['integral_cut'] = 0;
                    $temp['integral'] = intval($row['num']);
                }
				if(key_exists($row["employee_id"],$arrYear)){ //扣除5年過期的學分
				    $cut_num = intval($arrYear[$row["employee_id"]]);
                    $temp['integral'] -= $cut_num;
                }
				$temp['s_city'] = CGeneral::getCityName($row['s_city']);
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