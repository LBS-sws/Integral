<?php

class AuditAddList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('integral','ID'),
            'activity_name'=>Yii::t('integral','Credit activities Name'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'set_id'=>Yii::t('integral','Integral Name'),
            'integral'=>Yii::t('integral','Integral Num'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'state'=>Yii::t('integral','Status'),
            'lcd'=>Yii::t('integral','apply for time'),
        );
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,e.name AS activity_name,b.integral_name,d.name AS employee_name,d.city AS s_city from gr_gral_add a
                LEFT JOIN gr_act_add e ON a.activity_id = e.id
                LEFT JOIN gr_integral_add b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) 
			";
        $sql2 = "select count(a.id) from gr_gral_add a
                LEFT JOIN gr_act_add e ON a.activity_id = e.id
                LEFT JOIN gr_integral_add b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 1) 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'activity_name':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.employee_name',$svalue);
                    break;
                case 'integral_name':
                    $clause .= General::getSqlConditionClause('b.integral_name',$svalue);
                    break;
                case 'integral':
                    $clause .= General::getSqlConditionClause('a.integral',$svalue);
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
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = $this->getListStatus($record['state']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'activity_name'=>$record['activity_name'],
                    'integral_name'=>$record['integral_name'],
                    'integral'=>$record['integral'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['AuditAdd_ya01'] = $this->getCriteria();
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
