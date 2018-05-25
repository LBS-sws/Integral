<?php

class IntegralCutView extends CListPageModel
{
    public $activity_id;

	public function attributeLabels()
	{
		return array(
			'integral_name'=>Yii::t('integral','Cut Name'),
            'integral_num'=>Yii::t('integral','Cut Integral'),
            'inventory'=>Yii::t('integral','inventory'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from gr_integral_cut
				where id >= 0 
			";
		$sql2 = "select count(id)
				from gr_integral_cut
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'integral_name':
					$clause .= General::getSqlConditionClause('integral_name', $svalue);
					break;
				case 'integral_num':
					$clause .= General::getSqlConditionClause('integral_num', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'integral_name'=>$record['integral_name'],
						'integral_num'=>$record['integral_num'],
						'inventory'=>$record['inventory'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['integralView_op01'] = $this->getCriteria();
		return true;
	}
	//獲取當前用戶的可用積分
	function getNowIntegral($staffId=0,$lcd=""){
	    if(empty($lcd)){
	        $year = date("Y");
        }else{
            $year = date("Y",strtotime($lcd));
        }
        $year = intval($year);
        $startDate = "$year-01-01 00:00:00";
        $lastDate = "$year-12-31 23:59:59";
	    if(empty($staffId)){
            $staffId = Yii::app()->user->staff_id();
        }
        $dateSql = " and lcd >='$startDate' and lcd <='$lastDate'";
        $validitySql = " and a.lcd >='".($year-4)."-01-01 00:00:00' and a.lcd <='".($year-1)."-12-31 23:59:59'";
        $command = Yii::app()->db->createCommand();
        $sumIntegral = $command->select("sum(integral)")->from("gr_gral_add")
            ->where("employee_id=:employee_id and state=3 $dateSql",array(":employee_id"=>$staffId))->queryScalar();
        $sumIntegral = empty($sumIntegral)?0:intval($sumIntegral); //當年總積分
        $command->reset();
        $integral = $command->select("sum(apply_num*integral)")->from("gr_gral_cut")
            ->where("employee_id=:employee_id and state in (1,3) $dateSql",array(":employee_id"=>$staffId))->queryScalar();
        $integral = empty($integral)?0:intval($integral);//當年兌換的積分
        $integral = $sumIntegral-$integral;
        $command->reset();
        $add = $command->select("sum(a.integral)")->from("gr_gral_add a")
            ->leftJoin("gr_integral_add b","a.set_id=b.id")
            ->where("b.validity = 5 and a.employee_id=:employee_id and a.state=3 $validitySql",array(":employee_id"=>$staffId))->queryScalar();
        $add = empty($add)?0:intval($add);//有5年期限的積分
        $sumIntegral+=$add;


        return array(
            "cut"=>$integral,//可用積分
            "sum"=>$sumIntegral,//總積分
        );
    }

}
