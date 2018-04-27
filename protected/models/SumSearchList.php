<?php

class SumSearchList extends CListPageModel
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
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'sum_integral'=>Yii::t('integral','Sum Integral'),
            'ranking'=>Yii::t('integral','ranking'),
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
		$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		$staffId = Yii::app()->user->staff_id();//
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select SUM(integral) AS sum_integral,a.employee_id,d.name AS employee_name,d.city AS s_city from gr_integral a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND (a.state=3) AND a.alg_con = 0 
			";
		$sql2 = "select SUM(integral) AS sum_integral,a.employee_id,d.name AS employee_name,d.city AS s_city from gr_integral a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND (a.state=3) AND a.alg_con = 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
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
        $clause.=" GROUP BY a.employee_id ";
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
            $order = " order by a.employee_id desc";

        $sql = $sql1.$clause;
        $row = Yii::app()->db->createCommand($sql)->queryAll();
        if($row){
            $this->totalRow = count($row);
        }else{
            $this->totalRow = 0;
        }

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                //$colorList = $this->statusToColor($record['state']);
				$this->attr[] = array(
					'id'=>$record['employee_id'],
					'employee_name'=>$record['employee_name'],
					'sum_integral'=>$record['sum_integral'],
                    'city'=>CGeneral::getCityName($record["s_city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$searchList = $this->getCriteria();
        $searchList["searchTimeStart"] = $this->searchTimeStart;
        $searchList["searchTimeEnd"] = $this->searchTimeEnd;
		$session['sumSearch_01'] = $searchList;
		return true;
	}

}
