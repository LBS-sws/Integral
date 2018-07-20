<?php

class AuditGiftList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('integral','ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'gift_name'=>Yii::t('integral','Cut Name'),
            'bonus_point'=>Yii::t('integral','Cut Integral'),
            'apply_num'=>Yii::t('integral','Number of applications'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'state'=>Yii::t('integral','Status'),
            'apply_date'=>Yii::t('integral','apply for time'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		$staffId = Yii::app()->user->staff_id();//
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.gift_name,d.name AS employee_name,d.city AS s_city from gr_gift_request a
                LEFT JOIN gr_gift_type b ON a.gift_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) 
			";
        $sql2 = "select count(a.id) from gr_gift_request a
                LEFT JOIN gr_gift_type b ON a.gift_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'gift_name':
                    $clause .= General::getSqlConditionClause('b.gift_name',$svalue);
                    break;
                case 'bonus_point':
                    $clause .= General::getSqlConditionClause('a.bonus_point',$svalue);
                    break;
                case 'apply_num':
                    $clause .= General::getSqlConditionClause('a.apply_num',$svalue);
                    break;
                case 'lcd':
                    $clause .= General::getSqlConditionClause('a.lcd',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and d.city in '.IntegralForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
            $order = " order by a.id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = $this->statusToColor($record['state']);
				$this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'gift_name'=>$record['gift_name'],
                    'bonus_point'=>$record['bonus_point'],
                    'apply_num'=>$record['apply_num'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['auditGift_01'] = $this->getCriteria();
		return true;
	}

    //根據狀態獲取顏色
    public function statusToColor($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("integral","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
            default:
                return array(
                    "status"=>Yii::t("integral","Error"),
                    "style"=>" "
                );//已拒絕
        }
    }
}
