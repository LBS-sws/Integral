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
}
