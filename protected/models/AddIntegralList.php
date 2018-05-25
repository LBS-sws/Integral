<?php

class AddIntegralList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('integral','Credit activities Name'),
            'start_time'=>Yii::t('integral','Start time'),
            'end_time'=>Yii::t('integral','End time'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from gr_act_add
				where id >= 0 
			";
		$sql2 = "select count(id)
				from gr_act_add
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
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
						'name'=>$record['name'],
						'start_time'=>CGeneral::toDate($record['start_time']),
						'end_time'=>CGeneral::toDate($record['end_time']),
						'status'=>$this->getStatus($record),
					);
			}
		}
		$session = Yii::app()->session;
		$session['addIntegral_op01'] = $this->getCriteria();
		return true;
	}

	private function getStatus($row){
        $str = "";
        $color = "";
        $bool = false;
        $date = date("Y-m-d");
        if(strtotime($date)<strtotime($row["start_time"])){
            $color = " text-danger";
            $str = Yii::t("integral","Not at the");//未開始
        }elseif (strtotime($date)<=strtotime($row["end_time"])){
            $color = " text-primary";
            if(Yii::app()->user->validRWFunction('DE01')||Yii::app()->user->validFunction('ZR01')){
                $bool = true;
            }
            $str = Yii::t("integral","ongoing");//進行中
        }else{
            $str = Yii::t("integral","Has ended");//已結束
            $color = " text-warning";
        }
        return array(
            "str"=>$str,
            "color"=>$color,
            "bool"=>$bool,
        );
    }
}
