<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CutSearchForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
	public $alg_con = 1;
	public $set_id;
	public $set_name;
	public $integral;
	public $apply_num;
	public $images_url;
	public $remark;
	public $reject_note;
	public $state = 0;
	public $city;
	public $lcu;
	public $luu;
	public $lcd;
	public $lud;
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
            'set_id'=>Yii::t('integral','Cut Name'),
            'integral'=>Yii::t('integral','Cut Integral'),
            'apply_num'=>Yii::t('integral','Number of applications'),
			'remark'=>Yii::t('integral','Remark'),
            'reject_note'=>Yii::t('integral','Reject Note'),
            'city'=>Yii::t('integral','City'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id, employee_name, alg_con, set_id, integral, images_url, remark, reject_not, apply_num, set_name, lcu, luu, lcd, lud','safe'),
			array('set_id','required'),
			array('apply_num','required'),
			array('set_id','validateIntegral'),
            array('apply_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

	public function validateIntegral($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("integral_num,inventory")->from("gr_integral_cut")
            ->where("id=:id", array(':id'=>$this->set_id))->queryRow();
        if ($rows){
            $num = IntegralCutList::getNowUserIntegralCut();
            if(intval($num) < intval($rows["integral_num"])){
                $message = Yii::t('integral','Lack of integral');//積分不足
                $this->addError($attribute,$message);
            }else{
                if(intval($this->apply_num)>intval($rows["inventory"])){
                    $message = Yii::t('integral','Insufficient inventory');//積分不足
                    $this->addError($attribute,$message);
                }else{
                    $this->integral = $rows["integral_num"];
                    $this->state = 1;
                }
            }
        }else{
            $message = Yii::t('integral','Integral Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name")->from("gr_integral a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.state = 3 and a.alg_con = 1 and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
				$this->alg_con = $row['alg_con'];
                $this->set_id = $row['set_id'];
                $this->set_name = IntegralCutForm::getIntegralCutNameToId($row['set_id']);
                $this->integral = $row['integral'];
                $this->apply_num = $row['apply_num'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
				break;
			}
		}
		return true;
	}

}
