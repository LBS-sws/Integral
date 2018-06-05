<?php

class CutSearchList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('integral','ID'),
            'activity_name'=>Yii::t('integral','Cut activities Name'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'set_id'=>Yii::t('integral','Cut Name'),
            'integral'=>Yii::t('integral','Cut Integral'),
            'apply_num'=>Yii::t('integral','Number of applications'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'lcd'=>Yii::t('integral','apply for time'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.integral_name,e.name AS activity_name,d.name AS employee_name,d.city AS s_city from gr_gral_cut a
                LEFT JOIN gr_act_cut e ON a.activity_id = e.id
                LEFT JOIN gr_integral_cut b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND a.state = 3 
			";
        $sql2 = "select count(a.id) from gr_gral_cut a
                LEFT JOIN gr_act_cut e ON a.activity_id = e.id
                LEFT JOIN gr_integral_cut b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND a.state = 3 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
                case 'activity_name':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
				case 'integral_name':
					$clause .= General::getSqlConditionClause('b.integral_name',$svalue);
					break;
				case 'integral':
					$clause .= General::getSqlConditionClause('a.integral',$svalue);
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
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.lcd >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.lcd <='$svalue 23:59:59' ";
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
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_name'=>$record['employee_name'],
                    'activity_name'=>$record['activity_name'],
					'integral_name'=>$record['integral_name'],
					'integral'=>$record['integral'],
					'apply_num'=>$record['apply_num'],
					'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                    'city'=>CGeneral::getCityName($record["s_city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['cutSearch_01'] = $this->getCriteria();
		return true;
	}

}
