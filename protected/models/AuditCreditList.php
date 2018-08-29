<?php

class AuditCreditList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('integral','ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'credit_type'=>Yii::t('integral','Integral Name'),
            'credit_name'=>Yii::t('integral','Integral Name'),
            'credit_point'=>Yii::t('integral','Integral Num'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'state'=>Yii::t('integral','Status'),//狀態 0：草稿 1：發送  2：拒絕  3：完成  4:確定
            'apply_date'=>Yii::t('integral','apply for time'),
            'category'=>Yii::t('integral','integral type'),
        );
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,b.category,b.credit_name,d.name AS employee_name,d.city AS s_city from gr_credit_request a
                LEFT JOIN gr_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 4) 
			";
        $sql2 = "select count(a.id) from gr_credit_request a
                LEFT JOIN gr_credit_type b ON a.credit_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where (d.city IN ($city_allow) AND a.state = 4) 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'credit_name':
                    $clause .= General::getSqlConditionClause('b.credit_name',$svalue);
                    break;
                case 'credit_point':
                    $clause .= General::getSqlConditionClause('a.credit_point',$svalue);
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

        $categoryList = CreditTypeForm::getCategoryAll();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = $this->getListStatus($record['state']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'category'=>$categoryList[$record['category']],
                    'employee_name'=>$record['employee_name'],
                    'credit_name'=>$record['credit_name'],
                    'credit_point'=>$record['credit_point'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['auditCredit_ya01'] = $this->getCriteria();
		return true;
	}


    public function getListStatus($status){
        switch ($status){
            case 4:
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
