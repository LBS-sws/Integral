<?php
class RptStretchList extends CReport {
	protected function fields() {
		$arr =  array(
			'employee_code'=>array('label'=>Yii::t('integral','Employee Code'),'width'=>22,'align'=>'L'),
			'employee_name'=>array('label'=>Yii::t('integral','Employee Name'),'width'=>22,'align'=>'L'),
            's_city'=>array('label'=>Yii::t('integral','City'),'width'=>20,'align'=>'L'),
		);
        $rows = Yii::app()->db->createCommand()->select("id,prize_name")->from("gr_prize_type")->order("z_index desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $num = $row["id"];
                $arr["stretch_$num"] = array('label'=>$row["prize_name"],'width'=>20,'align'=>'L');
            }
            $arr["stretch_sum"] = array('label'=>Yii::t('integral','Three consecutive championships'),'width'=>30,'align'=>'L');
        }
		return $arr;
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('app','Stretch List Report').' / '
			.Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
        $city_allow = $this->criteria['city_allow'];

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
                $cond_staff = " and a.employee_id in ($cond_staff) ";
            } 
		}
        $headList = Yii::app()->db->createCommand()->select("id,prize_name")->from("gr_prize_type")->order("z_index desc")->queryAll();
        $prize_sql = "";
        if($headList){
            foreach ($headList as $head){
                $prize_sql.=",SUM(CASE WHEN a.prize_type='".$head["id"]."' THEN 1 ELSE 0 END) AS stretch_".$head["id"];
            }
        }

        $sql = "select d.code AS employee_code,d.name AS employee_name,d.city AS s_city$prize_sql from gr_prize_request a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and d.staff_status = 0 and a.state = 3 $cond_staff $cond_city 
                GROUP BY a.employee_id 
                ORDER BY d.city DESC 
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['employee_code'] = $row['employee_code'];
				$temp['employee_name'] = $row['employee_name'];
                $temp['s_city'] = CGeneral::getCityName($row['s_city']);

                if($headList){
                    $prize_sum = 0;
                    foreach ($headList as $item){
                        $key = "stretch_".$item["id"];
                        $temp[$key]=$row[$key];
                        if (strpos($item["prize_name"],'金奖')!==false||strpos($item["prize_name"],'金獎')!==false)
                            $prize_sum = floor(intval($row[$key])/3);
                    }
                    $temp["stretch_sum"]=$prize_sum;
                }
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