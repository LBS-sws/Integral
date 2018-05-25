<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditCutForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $activity_id;
	public $employee_id;
	public $employee_name;
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
            'activity_id'=>Yii::t('integral','Cut activities Name'),
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
			array('id, employee_id, employee_name, set_id, integral, images_url, remark, reject_not, apply_num, set_name, lcu, luu, lcd, lud','safe'),
            array('reject_note','required',"on"=>"reject"),
		);
	}

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name")->from("gr_gral_cut a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.state = 1 and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->activity_id = $row['activity_id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
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
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
		$model = new CutForm();
		$model->retrieveData($this->id);
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
            case 'audit':
                $sql = "update gr_gral_cut set
							state = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update gr_gral_cut set
							state = 2, 
							reject_note = :reject_note, 
							luu = :luu
						where id = :id
						";
                break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':reject_note')!==false)
            $command->bindParam(':reject_note',$this->reject_note,PDO::PARAM_STR);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='reject'){
            $this->id = Yii::app()->db->getLastInsertID();
            //庫存补回
            Yii::app()->db->createCommand("update gr_integral_cut set inventory=inventory+".$model->apply_num." where id=".$model->set_id)->execute();
        }
        return true;
	}
}
