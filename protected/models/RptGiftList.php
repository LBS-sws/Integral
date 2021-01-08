<?php
class RptGiftList extends CReport {
	protected function fields() {
		return array(
			'employee_code'=>array('label'=>Yii::t('integral','Employee Code'),'width'=>15,'align'=>'L'),//員工
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),//員工
            's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),//城市
            'year'=>array('label'=>Yii::t('integral','particular year'),'width'=>15,'align'=>'L'),//年份
			'sum_gift'=>array('label'=>Yii::t('integral','Sum Gift'),'width'=>15,'align'=>'C'),//
			'sum_apply'=>array('label'=>Yii::t('integral','Apply Gift'),'width'=>15,'align'=>'C'),//
			'num'=>array('label'=>Yii::t('integral','Available Gift'),'width'=>15,'align'=>'C'),//
		);
	}
	
	public function genReport() {//Sum Gift Report
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('app','Sum Gift Report').':'.$this->criteria['YEAR'].' / '
			.Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$year = $this->criteria['YEAR'];
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
        $city_allow = $this->criteria['city_allow'];

        $startDate = "$year-01-01";
        $lastDate = "$year-12-31";
        $cond_city = "";
        if (!empty($city)) {
            $citylist = explode('~',$city);
            if(count($citylist)>1){
                $cond_city = implode("','",$citylist);
            }else{
                $cond_city = "'".reset($citylist)."'";
            }
            if ($cond_city!=''){
                $cond_city = " and d.city in ($cond_city) ";
            }
        }
		
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
                $cond_staff = " and employee_id in ($cond_staff) ";
            } 
		}

        $sql = "SELECT a.sum_gift,b.sum_apply,
                CASE WHEN(b.sum_apply is NULL || b.sum_apply=0) THEN a.sum_gift ELSE (a.sum_gift-b.sum_apply) END as num
                ,d.* FROM 
                (SELECT sum(bonus_point) as sum_gift,employee_id FROM gr_bonus_point WHERE rec_date >='$startDate' and rec_date <='$lastDate' $cond_staff GROUP BY employee_id) a
                LEFT JOIN ((SELECT sum(apply_num*bonus_point) as sum_apply,employee_id FROM gr_gift_request WHERE state in (1,3) and apply_date >='$startDate' and apply_date <='$lastDate' $cond_staff GROUP BY employee_id)) b
                ON a.employee_id = b.employee_id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                WHERE d.city IN ($city_allow) and d.staff_status = 0 $cond_city
			";

        $rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp['employee_code'] = $row['code'];
				$temp['employee_name'] = $row['name'];
				$temp['s_city'] = CGeneral::getCityName($row["city"]);
				$temp['year'] = $year.Yii::t("integral","year");
				$temp['sum_gift'] = $row['sum_gift'];
				$temp['sum_apply'] = empty($row['sum_apply'])?0:$row['sum_apply'];
				$temp['num'] = empty($row['sum_apply'])?$row['sum_gift']:$row['num'];
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