<?php

class GiftList extends CListPageModel
{

    public function attributeLabels()
    {
        return array(
            'gift_name'=>Yii::t('integral','Cut Name'),
            'bonus_point'=>Yii::t('integral','Cut Integral'),
            'inventory'=>Yii::t('integral','inventory'),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $city = Yii::app()->user->city();
        $sql1 = "select *
				from gr_gift_type
				where id >= 0 
			";
        $sql2 = "select count(id)
				from gr_gift_type
				where id >= 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'gift_name':
                    $clause .= General::getSqlConditionClause('gift_name', $svalue);
                    break;
                case 'bonus_point':
                    $clause .= General::getSqlConditionClause('bonus_point', $svalue);
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
                    'gift_name'=>$record['gift_name'],
                    'bonus_point'=>$record['bonus_point'],
                    'inventory'=>$record['inventory'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['gift_op01'] = $this->getCriteria();
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
        $startDate = "$year-01-01";
        $lastDate = "$year-12-31";
        if(empty($staffId)){
            $staffId = Yii::app()->user->staff_id();
        }
        $dateSql = " and rec_date >='$startDate' and rec_date <='$lastDate'";
        $validitySql = " and apply_date >='$startDate' and apply_date <='$lastDate'";
        $command = Yii::app()->db->createCommand();
        $sumIntegral = $command->select("sum(bonus_point)")->from("gr_bonus_point")
            ->where("employee_id=:employee_id $dateSql",array(":employee_id"=>$staffId))->queryScalar();
        $sumIntegral = empty($sumIntegral)?0:intval($sumIntegral); //當年總積分
        $command->reset();
        $integral = $command->select("sum(apply_num*bonus_point)")->from("gr_gift_request")
            ->where("employee_id=:employee_id and state in (1,3) $validitySql",array(":employee_id"=>$staffId))->queryScalar();
        $integral = empty($integral)?0:intval($integral);//當年兌換的積分
        $integral = $sumIntegral-$integral;


        return array(
            "cut"=>$integral,//可用積分
            "sum"=>$sumIntegral,//總積分
        );
    }

}
