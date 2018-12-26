<?php

class GiftSearchList extends CListPageModel
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
            'employee_code'=>Yii::t('integral','Employee Code'),
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
        $sql1 = "select a.*,b.gift_name,d.name AS employee_name,d.code AS employee_code,d.city AS s_city from gr_gift_request a
                LEFT JOIN gr_gift_type b ON a.gift_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 3 and d.staff_status = 0 
			";
        $sql2 = "select count(a.id) from gr_gift_request a
                LEFT JOIN gr_gift_type b ON a.gift_type = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) and a.state = 3 and d.staff_status = 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('d.code',$svalue);
                    break;
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
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') >='$svalue' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and date_format(a.apply_date,'%Y/%m/%d') <='$svalue' ";
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
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'gift_name'=>$record['gift_name'],
                    'bonus_point'=>$record['bonus_point'],
                    'apply_num'=>$record['apply_num'],
                    'apply_date'=>date("Y-m-d",strtotime($record['apply_date'])),
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['giftSearch_01'] = $this->getCriteria();
        return true;
    }
}
