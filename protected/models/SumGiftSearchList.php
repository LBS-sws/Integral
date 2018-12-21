<?php

class SumGiftSearchList extends CListPageModel
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
            'code'=>Yii::t('integral','Employee Code'),
            'name'=>Yii::t('integral','Employee Name'),
            'year'=>Yii::t('integral','particular year'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
            'sum_gift'=>Yii::t('integral','Sum Gift'),
            'sum_apply'=>Yii::t('integral','Apply Gift'),
            'num'=>Yii::t('integral','Available Gift'),
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
        $city_allow = Yii::app()->user->city_allow();
        if (!empty($this->year)) {
            $year = str_replace("'","\'",$this->year);
            if(!is_numeric($year)){
                $year = date("Y");
            }
        }else{
            $year = date("Y");
        }
        $this->year = $year;
        $startDate = "$year-01-01";
        $lastDate = "$year-12-31";
        $sql1 = "SELECT a.sum_gift,b.sum_apply,(a.sum_gift-b.sum_apply) as num,d.* FROM 
                (SELECT sum(bonus_point) as sum_gift,employee_id FROM gr_bonus_point WHERE rec_date >='$startDate' and rec_date <='$lastDate' GROUP BY employee_id) a
                LEFT JOIN ((SELECT sum(apply_num*bonus_point) as sum_apply,employee_id FROM gr_gift_request WHERE state in (1,3) and apply_date >='$startDate' and apply_date <='$lastDate' GROUP BY employee_id)) b
                ON a.employee_id = b.employee_id
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                WHERE d.city IN ($city_allow) 
			";
        $sql2 = "SELECT count(*) FROM 
                (SELECT sum(bonus_point) as sum_gift,employee_id FROM gr_bonus_point WHERE rec_date >='$startDate' and rec_date <='$lastDate' GROUP BY employee_id) a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                WHERE d.city IN ($city_allow) 
          ";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'code':
                    $clause .= General::getSqlConditionClause('d.code',$svalue);
                    break;
                case 'name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'city_name'://
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by d.city desc";


        //$group = "GROUP BY a.employee_id,a.year ";s
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
                    'code'=>$record['code'],
                    'name'=>$record['name'],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'year'=>$this->year.Yii::t("integral","year"),
                    'sum_gift'=>$record['sum_gift'],
                    'sum_apply'=>empty($record['sum_apply'])?0:$record['sum_apply'],
                    'num'=>empty($record['sum_apply'])?$record['sum_gift']:$record['num'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['sumGiftSearch_op01'] = $this->getCriteria();
        return true;
    }

    public function getYearList(){
        $arr=array();
        for ($i=2015;$i<=2025;$i++){
            $arr[$i] = $i.Yii::t("integral","year");
        }
        return $arr;
    }
}
