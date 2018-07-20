<?php

class CreditTypeList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
            'credit_code'=>Yii::t('integral','Integral Code'),
			'credit_name'=>Yii::t('integral','Integral Name'),
            'credit_point'=>Yii::t('integral','Integral Num'),
            'rule'=>Yii::t('integral','integral conditions'),
            'category'=>Yii::t('integral','integral type'),
            'validity'=>Yii::t('integral','validity'),
            'rule'=>Yii::t('integral','integral conditions'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from gr_credit_type
				where id >= 0 
			";
		$sql2 = "select count(id)
				from gr_credit_type
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'credit_code':
					$clause .= General::getSqlConditionClause('credit_code', $svalue);
					break;
				case 'credit_name':
					$clause .= General::getSqlConditionClause('credit_name', $svalue);
					break;
				case 'credit_point':
					$clause .= General::getSqlConditionClause('credit_point', $svalue);
					break;
				case 'category':
					$clause .= General::getSqlConditionClause('category', $svalue);
					break;
				case 'validity':
					$clause .= General::getSqlConditionClause('validity', $svalue);
					break;
				case 'rule':
					$clause .= General::getSqlConditionClause('rule', $svalue);
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
						'credit_code'=>$record['credit_code'],
						'credit_name'=>$record['credit_name'],
						'credit_point'=>$record['credit_point'],
						'validity'=>$record['validity'],
						'rule'=>$record['rule'],
						'category'=>$this->getCategoryToNum($record['category']),
					);
			}
		}
		$session = Yii::app()->session;
		$session['CreditType_op01'] = $this->getCriteria();
		return true;
	}

	public function getCategoryToNum($num){
        $typeList = CreditTypeForm::getCategoryAll();
        if(key_exists($num,$typeList)){
            return $typeList[$num];
        }
        return $num;
    }
}
