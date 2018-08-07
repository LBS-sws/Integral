<?php
class RptYearList extends CReport {
	protected function fields() {
		return array(
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),//員工
            's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),//城市
            'year'=>array('label'=>Yii::t('integral','particular year'),'width'=>20,'align'=>'L'),//城市
			'start_num'=>array('label'=>Yii::t('integral','Sum Integral'),'width'=>25,'align'=>'C'),//總學分
			'end_num'=>array('label'=>Yii::t('integral','Cut Integral'),'width'=>25,'align'=>'C'),//當前可用學分
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

		$dateSql = "";
		if (!empty($year)){
		    $dateSql.=" and a.year = '$year' ";
        }
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
        $sql = "select a.year,d.name AS employee_name,d.city AS s_city,SUM(a.start_num) AS start_num,SUM(a.end_num) AS end_num from gr_credit_point_ex a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($citylist) 
                $cond_staff $dateSql
                GROUP BY a.employee_id,a.year 
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp['employee_name'] = $row['employee_name'];
				$temp['s_city'] = CGeneral::getCityName($row["s_city"]);
				$temp['year'] = $row['year'].Yii::t("integral","year");
				$temp['start_num'] = $row['start_num'];
				$temp['end_num'] = $row['end_num'];
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