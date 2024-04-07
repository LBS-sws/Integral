<?php

class DeductionSearchList extends CListPageModel
{
    public $category;//學分類型

    public function getCriteria() {
        return array(
            'category'=>$this->category,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
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
            'start_num'=>Yii::t('integral','start credit num'),
            'end_num'=>Yii::t('integral','effect credit num'),
            'city'=>Yii::t('integral','City'),
            'category'=>Yii::t('integral','integral type'),
            'credit_name'=>Yii::t('integral','Integral Name'),
            'city_name'=>Yii::t('integral','City'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, category','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $year = date("Y");
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.year,f.category,f.credit_name,d.code AS employee_code,d.name AS employee_name,d.city AS s_city,a.start_num,a.end_num from gr_credit_point_ex a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id 
                LEFT JOIN gr_credit_point e ON a.point_id = e.id
                LEFT JOIN gr_credit_type f ON e.credit_type = f.id
                where d.city IN ($city_allow) and d.staff_status = 0 and a.year = '$year' 
			";
        $sql2 = "select count(a.id) from gr_credit_point_ex a
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id 
                LEFT JOIN gr_credit_point e ON a.point_id = e.id
                LEFT JOIN gr_credit_type f ON e.credit_type = f.id
                where d.city IN ($city_allow)  and d.staff_status = 0  and a.year = '$year' 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'credit_name':
                    $clause .= General::getSqlConditionClause('f.credit_name',$svalue);
                    break;
                case 'city_name'://
                    $clause .= ' and d.city in '.CreditRequestList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->category)) {
            $category = str_replace("'","\'",$this->category);
            $clause .= " and f.category = '$category' ";
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
            $categoryList = CreditTypeForm::getCategoryAll();
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'category'=>$categoryList[$record['category']],
                    'credit_name'=>$record['credit_name'],
                    'start_num'=>$record['start_num'],
                    'end_num'=>$record['end_num'],
                    'year'=>$record['year'].Yii::t("integral","year"),
                    'city'=>CGeneral::getCityName($record["s_city"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['deductionSearch_op01'] = $this->getCriteria();
        return true;
    }

    public function getCategoryAll(){
        $arr=CreditTypeForm::getCategoryAll();
        $arr[""]="-- ".Yii::t('integral','integral type')." --";
        return $arr;
    }
}
