<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class GiftRequestForm extends CFormModel
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
			array('id, employee_id, employee_name, gift_type, bonus_point, remark, reject_not, apply_num, gift_name, lcu, luu, lcd, lud','safe'),
			array('gift_type','required'),
			array('apply_num','required'),
			array('gift_type','validateIntegral'),
            array('apply_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

	public function validateIntegral($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("bonus_point,inventory")->from("gr_gift_type")
            ->where("id=:id and city in ($city_allow)", array(':id'=>$this->gift_type))->queryRow();
        if ($rows){
            $num = GiftList::getNowIntegral();
            $num = $num['cut'];
            if(intval($num) < intval($rows["bonus_point"])*intval($this->apply_num)){
                $message = Yii::t('integral','Lack of integral');//積分不足
                $this->addError($attribute,$message);
            }else{
                if(intval($this->apply_num)>intval($rows["inventory"])){
                    $message = Yii::t('integral','Insufficient inventory');//庫存不足
                    $this->addError($attribute,$message);
                }else{
                    $this->bonus_point = $rows["bonus_point"];
                    $this->state = 1;
                }
            }
        }else{
            $message = Yii::t('integral','Integral Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //积分删除
    public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("gr_gift_request")
            ->where('id=:id and state in (0,2)', array(':id'=>$this->id))->queryRow();
        if ($rows){
            return true; //允許刪除
        }
        return false;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,c.gift_name,b.name as employee_name")->from("gr_gift_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_gift_type c","a.gift_type = c.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
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
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        $staffId = Yii::app()->user->staff_id();
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from gr_gift_request where id = :id and city IN ($city_allow)";
				break;
			case 'apply':
				$sql = "insert into gr_gift_request(
							employee_id, gift_type, bonus_point, apply_num, remark, state, city, lcu, apply_date
						) values (
							:employee_id, :gift_type, :bonus_point, :apply_num, :remark, :state, :city, :lcu, :apply_date
						)";
				break;
            case 'audit':
                $sql = "update gr_gift_request set
							bonus_point = :bonus_point,
							apply_num = :apply_num,
							remark = :remark,
							reject_note = '',
							state = :state,
							luu = :luu,
							apply_date = :apply_date
						where id = :id
						";
                break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$staffId,PDO::PARAM_STR);
		if (strpos($sql,':gift_type')!==false)
			$command->bindParam(':gift_type',$this->gift_type,PDO::PARAM_INT);
		if (strpos($sql,':apply_num')!==false)
			$command->bindParam(':apply_num',$this->apply_num,PDO::PARAM_INT);
		if (strpos($sql,':bonus_point')!==false)
			$command->bindParam(':bonus_point',$this->bonus_point,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':state')!==false)
			$command->bindParam(':state',$this->state,PDO::PARAM_STR);

        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='apply'||$this->scenario=='audit'){
            //扣除庫存
            Yii::app()->db->createCommand("update gr_gift_type set inventory=inventory-".$this->apply_num." where id=".$this->gift_type)->execute();
        }

        $this->sendEmail();
        return true;
	}

    //發送郵件
    protected function sendEmail(){
        if($this->state == 1){
            $email = new Email();
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.code as employee_code,b.city as s_city")
                ->from("gr_gift_request a")
                ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
                ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
            $description="积分兑换申请 - ".$row["employee_name"];
            $subject="积分兑换申请 - ".$row["employee_name"];
            $message="<p>员工编号：".$row["employee_code"]."</p>";
            $message.="<p>员工姓名：".$row["employee_name"]."</p>";
            $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
            $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
            $message.="<p>扣除积分：".(floatval($row["bonus_point"])*floatval($row["apply_num"]))."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("GA02",$row["s_city"]);
            $email->sent();
        }
    }


    //驗證當前用戶的權限
    public function validateNowUser($bool = false){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.name")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            if($bool){
                $this->employee_id = $rs["id"];
                $this->employee_name = $rs["name"];
            }
            return true; //已綁定員工
        }else{
            return false;
        }
    }
}
