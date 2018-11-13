<?php

class StretchSearchList extends CListPageModel
{
    public $city;//
    public $prize_list;//
    public $prize_sql;//
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
            'year'=>Yii::t('integral','particular year'),
            'start_num'=>Yii::t('integral','sum credit num'),
            'end_num'=>Yii::t('integral','effect credit num'),
            'city'=>Yii::t('integral','City'),
            'city_name'=>Yii::t('integral','City'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, city','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $this->getPrizeAll();
        $prize_sql = $this->prize_sql;
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select d.code AS employee_code,d.name AS employee_name,d.city AS s_city$prize_sql from gr_prize_request a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow)  and d.staff_status = 0 and a.state = 3 
			";
        $sql2 = "select d.name AS employee_name$prize_sql from gr_prize_request a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city IN ($city_allow)  and d.staff_status = 0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'city_name'://
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->city)) {
            $city = str_replace("'","\'",$this->city);
            $clause .= " and d.city = '$city' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by a.employee_id desc";


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

        $list = $this->prize_list;
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $prize_sum = 0;
                $arr = array(
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
                foreach ($list as $item){
                    $key = "prize_".$item["id"];
                    $arr[$key]=$record[$key];
                    if (strpos($item["prize_name"],'金奖')!==false||strpos($item["prize_name"],'金獎')!==false)
                        $prize_sum = floor(intval($record[$key])/3);
                }
                $arr["prize_sum"]=$prize_sum;
                $this->attr[] = $arr;
            }
        }
        $session = Yii::app()->session;
        $session['stretchSearch_op01'] = $this->getCriteria();
        return true;
    }

//獲取城市列表
    public function getCityAllList(){
        $city_allow = Yii::app()->user->city_allow();
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code,name")->from($from)->where("code in ($city_allow)")->queryAll();
        $arr = array(""=>" -- ".Yii::t("user","City")." -- ");
        foreach ($rows as $row){
            $arr[$row["code"]] = $row["name"];
        }
        return $arr;
    }

    private function getPrizeAll(){
        $rows = Yii::app()->db->createCommand()->select("id,prize_name")->from("gr_prize_type")->order("z_index desc")->queryAll();
        $sql = "";
        if($rows){
            foreach ($rows as $row){
                $sql.=",SUM(CASE WHEN a.prize_type='".$row["id"]."' THEN 1 ELSE 0 END) AS prize_".$row["id"];
            }
        }
        $this->prize_list = $rows;
        $this->prize_sql = $sql;
    }
}
