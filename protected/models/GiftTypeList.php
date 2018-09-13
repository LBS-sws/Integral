<?php

class GiftTypeList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'gift_name'=>Yii::t('integral','Cut Name'),
            'bonus_point'=>Yii::t('integral','Cut Integral'),
            'inventory'=>Yii::t('integral','inventory'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from gr_gift_type
				where city in ($city_allow) 
			";
		$sql2 = "select count(id)
				from gr_gift_type
				where city in ($city_allow) 
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
                case 'city_name':
                    $clause .= ' and city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
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
                        'city'=>CGeneral::getCityName($record["city"]),
					);
			}
		}
		$session = Yii::app()->session;
		$session['giftType_op01'] = $this->getCriteria();
		return true;
	}
}
