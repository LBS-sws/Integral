<?php

class AuditPrizeList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('integral','ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'prize_name'=>Yii::t('integral','Prize Name'),
            'prize_point'=>Yii::t('integral','Prize Point'),
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
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,b.prize_name,d.name AS employee_name,d.city AS s_city from gr_prize_request a
                LEFT JOIN gr_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 1 
			";
        $sql2 = "select count(a.id) from gr_prize_request a
                LEFT JOIN gr_prize_type b ON a.prize_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 1 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'prize_name':
                    $clause .= General::getSqlConditionClause('b.prize_name',$svalue);
                    break;
                case 'prize_point':
                    $clause .= General::getSqlConditionClause('a.prize_point',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
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

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = $this->getListStatus($record['state']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'prize_name'=>$record['prize_name'],
                    'prize_point'=>$record['prize_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['auditPrize_ya01'] = $this->getCriteria();
		return true;
	}


    public function getListStatus($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("integral","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
/*            case 2:
                return array(
                    "status"=>Yii::t("integral","Rejected"),
                    "style"=>" text-red"
                );//已拒絕
            case 3:
                return array(
                    "status"=>Yii::t("integral","Finish approval"),
                    "style"=>" text-success"
                );//審核通過*/
            default:
                return array(
                    "status"=>Yii::t("integral","Error"),
                    "style"=>" "
                );//已拒絕
        }
    }
}
