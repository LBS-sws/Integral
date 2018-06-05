<?php

class SumSearchList extends CListPageModel
{
    public $year;//年
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
            'integral'=>Yii::t('integral','Integral Num'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, year','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $staffId = Yii::app()->user->staff_id();//
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select d.name AS employee_name,d.city AS s_city,SUM(a.integral) AS num from gr_gral_add a
                LEFT JOIN gr_act_add e ON a.activity_id = e.id
                LEFT JOIN gr_integral_add b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND a.state = 3 
			";
        $sql2 = "select count(a.id) from gr_gral_add a
                LEFT JOIN gr_act_add e ON a.activity_id = e.id
                LEFT JOIN gr_integral_add b ON a.set_id = b.id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow) AND a.state = 3 
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
        if (!empty($this->year)) {
            $year = str_replace("'","\'",$this->year);
            $yearOld = intval($year)-4;
            $clause .= " and ((a.lcd>='$year-01-01 00:00:00' and a.lcd<='$year-12-31 23:59:59' and b.validity=1)or(a.lcd>='$yearOld-01-01 00:00:00' and a.lcd<='$year-12-31 23:59:59' and b.validity=5)) ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by num desc";


        $group = "GROUP BY a.employee_id ";

        $sql = $sql1.$clause.$group;
        $count = Yii::app()->db->createCommand($sql)->queryAll();
        if($count){
            $this->totalRow = count($count);
        }else{
            $this->totalRow = 0;
        }

        $sql = $sql1.$clause.$group.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $list = array();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'employee_name'=>$record['employee_name'],
                    'integral'=>$record['num'],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['sumSearch_op01'] = $this->getCriteria();
        return true;
    }
}
