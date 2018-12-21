<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditGiftForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $gift_type;
    public $gift_name;
    public $bonus_point;
    public $apply_num;
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
            'gift_type'=>Yii::t('integral','Cut Name'),
            'bonus_point'=>Yii::t('integral','Cut Integral'),
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
			array('id, employee_id, employee_name, gift_type, bonus_point, remark, reject_not, apply_num, reject_note, gift_name, lcu, luu, lcd, lud','safe'),
            array('reject_note','required',"on"=>"reject"),
            array('id','required',"on"=>"reject"),
		);
	}

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,c.gift_name,b.name as employee_name")->from("gr_gift_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_gift_type c","a.gift_type = c.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.state = 1", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->gift_type = $row['gift_type'];
                $this->gift_name = $row['gift_name'];
                $this->bonus_point = $row['bonus_point'];
                $this->apply_num = $row['apply_num'];
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
		$model = new AuditGiftForm();
		$model->retrieveData($this->id);
		if(empty($model->id)){
            throw new CHttpException(404,'Cannot update.444');
        }
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
            case 'audit':
                $sql = "update gr_gift_request set
							state = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update gr_gift_request set
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
            //庫存补回
            Yii::app()->db->createCommand("update gr_gift_type set inventory=inventory+".$model->apply_num." where id=".$model->gift_type)->execute();
        }

        $this->sendEmail();
        return true;
	}

    //發送郵件
    protected function sendEmail(){
        $email = new Email();
        if($this->scenario == "audit"){
            $str = "积分兑换审核通过";
        }else{
            $str = "积分兑换被拒绝";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.code as employee_code,b.city as s_city")
            ->from("gr_gift_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
        $description="$str - ".$row["employee_name"];
        $subject="$str - ".$row["employee_name"];
        $message="<p>员工编号：".$row["employee_code"]."</p>";
        $message.="<p>员工姓名：".$row["employee_name"]."</p>";
        $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
        $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
        $message.="<p>扣除积分：".(floatval($row["bonus_point"])*floatval($row["apply_num"]))."</p>";
        if($this->scenario != "audit"){
            $message.="<p>拒绝原因：".$row["reject_note"]."</p>";
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->addEmailToStaffId($row["employee_id"]);
        $email->sent();
    }
}
