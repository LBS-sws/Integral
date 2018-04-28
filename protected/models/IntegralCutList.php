<?php

class IntegralCutList extends CListPageModel
{
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
		$session['integralCut_op01'] = $this->getCriteria();
		return true;
	}

	//獲取當前用戶的可用積分
	function getNowUserIntegralCut($staffId=0,$lcd=""){
	    if(empty($lcd)){
            $startDate = date("Y-01-01 00:00:00");
            $lastDate = date("Y-12-31 23:59:59");
        }else{
            $startDate = date("Y-01-01 00:00:00",strtotime($lcd));
            $lastDate = date("Y-12-31 23:59:59",strtotime($lcd));
        }
	    if(empty($staffId)){
            $staffId = Yii::app()->user->staff_id();
        }
        $dateSql = " and lcd >='$startDate' and lcd <='$lastDate'";
        $rows = Yii::app()->db->createCommand()->select("alg_con,integral,apply_num")
            ->from("gr_integral")->where("employee_id=:employee_id and ((state in (3,1) and alg_con = 1)or(state=3 and alg_con = 0))$dateSql",array(":employee_id"=>$staffId))->queryAll();
        $integral = 0;
        if($rows){
            foreach ($rows as $row){
                $num = intval($row["integral"])*intval($row["apply_num"]);
                if(intval($row["alg_con"]) == 1){
                    //兌換
                    $integral-=$num;
                }else{
                    $integral+=$num;
                }
            }
        }

        return $integral;
    }

	//獲取當前用戶的可用積分和總積分
	function getNowUserIntegralList($staffId=0,$lcd=""){
	    if(empty($lcd)){
            $startDate = date("Y-01-01 00:00:00");
            $lastDate = date("Y-12-31 23:59:59");
        }else{
            $startDate = date("Y-01-01 00:00:00",strtotime($lcd));
            $lastDate = date("Y-12-31 23:59:59",strtotime($lcd));
        }
	    if(empty($staffId)){
            $staffId = Yii::app()->user->staff_id();
        }
        $dateSql = " and lcd >='$startDate' and lcd <='$lastDate'";
        $rows = Yii::app()->db->createCommand()->select("alg_con,integral,apply_num")
            ->from("gr_integral")->where("employee_id=:employee_id and ((state in (3,1) and alg_con = 1)or(state=3 and alg_con = 0))$dateSql",array(":employee_id"=>$staffId))->queryAll();
        $integral = 0;
        $sumIntegral = 0;
        if($rows){
            foreach ($rows as $row){
                $num = intval($row["integral"])*intval($row["apply_num"]);
                if(intval($row["alg_con"]) == 1){
                    //兌換
                    $integral-=$num;
                }else{
                    $sumIntegral+=$num;
                    $integral+=$num;
                }
            }
        }

        return array(
            "integral"=>$integral,
            "sumIntegral"=>$sumIntegral,
        );
    }
}
