<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CreditSearchForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $credit_type;
    public $credit_point;
    public $images_url;
    public $apply_date;
    public $remark;
    public $reject_note;
    public $state = 0;
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;
    public $rule;
    public $integral_type;
    public $s_remark;
    public $confirm_date;
    public $audit_date;


    public $no_of_attm = array(
        'gral'=>0
    );
    public $docType = 'GRAL';
    public $docMasterId = array(
        'gral'=>0
    );
    public $files;
    public $removeFileId = array(
        'gral'=>0
    );
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('integral','Record ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'credit_type'=>Yii::t('integral','Integral Name'),
            'credit_point'=>Yii::t('integral','Integral Num'),
            'remark'=>Yii::t('integral','Remark'),
            'reject_note'=>Yii::t('integral','Reject Note'),
            'city'=>Yii::t('integral','City'),
            'rule'=>Yii::t('integral','integral conditions'),
            's_remark'=>Yii::t('integral','integral conditions'),
            'integral_type'=>Yii::t('integral','integral type'),
            'apply_date'=>Yii::t('integral','apply for time'),
            'audit_date'=>Yii::t('integral','audit for time'),
            'confirm_date'=>Yii::t('integral','confirm for time'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, employee_id, employee_name, credit_type, credit_point, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('employee_id','required'),
            array('credit_type','required'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
        );
    }

    public function retrieveData($index)
    {
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,d.rule,d.category,d.remark as s_remark,docman$suffix.countdoc('GRAL',a.id) as graldoc")
            ->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.state = 3", array(':id'=>$index))->queryAll();
        if (count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->credit_type = $row['credit_type'];
                $this->credit_point = $row['credit_point'];
                $this->apply_date = $row['apply_date'];
                $this->audit_date = $row['audit_date'];
                $this->confirm_date = $row['confirm_date'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->rule = $row['rule'];
                $this->city = $row['city'];
                $this->integral_type = $row['category'];
                $this->s_remark = $row['s_remark'];
                $this->no_of_attm['gral'] = $row['graldoc'];
                break;
            }
        }
        return true;
    }
}
