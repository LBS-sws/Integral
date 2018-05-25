<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CutForm extends CFormModel
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
            'employee_id'=>Yii::t('integral','Employee Name'),
            'activity_id'=>Yii::t('integral','Cut activities Name'),
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
			array('id, employee_id, employee_name, activity_id, set_id, integral, images_url, remark, reject_not, apply_num, set_name, lcu, luu, lcd, lud','safe'),
			array('set_id','required'),
			array('apply_num','required'),
			array('activity_id','required'),
			array('activity_id','validateActivity'),
			array('set_id','validateIntegral'),
            array('apply_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

	public function validateActivity($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("*")->from("gr_act_cut")
            ->where("id=:id", array(':id'=>$this->activity_id))->queryRow();
        if ($row){
            $date = date("Y-m-d");
            if(strtotime($date)<strtotime($row["start_time"])){
                $message = Yii::t('integral','Cut activities').Yii::t("integral","Not at the");//未開始
                $this->addError($attribute,$message);
            }elseif (strtotime($date)>strtotime($row["end_time"])){
                $message = Yii::t('integral','Cut activities').Yii::t("integral","Has ended");//已結束
                $this->addError($attribute,$message);
            }
        }else{
            $message = Yii::t('integral','Cut activities Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function validateIntegral($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("integral_num,inventory")->from("gr_integral_cut")
            ->where("id=:id", array(':id'=>$this->set_id))->queryRow();
        if ($rows){
            $num = IntegralCutView::getNowIntegral();
            $num = $num['cut'];
            if(intval($num) < intval($rows["integral_num"])*intval($this->apply_num)){
                $message = Yii::t('integral','Lack of integral');//積分不足
                $this->addError($attribute,$message);
            }else{
                if(intval($this->apply_num)>intval($rows["inventory"])){
                    $message = Yii::t('integral','Insufficient inventory');//庫存不足
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

    //积分删除
    public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("gr_gral_cut")
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
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name")->from("gr_gral_cut a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
				$this->activity_id = $row['activity_id'];
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
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        $staffId = Yii::app()->user->staff_id();
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from gr_gral_cut where id = :id and city IN ($city_allow)";
				break;
			case 'apply':
				$sql = "insert into gr_gral_cut(
							employee_id, activity_id, set_id, integral, apply_num, remark, state, city, lcu
						) values (
							:employee_id, :activity_id, :set_id, :integral, :apply_num, :remark, :state, :city, :lcu
						)";
				break;
            case 'audit':
                $sql = "update gr_gral_cut set
							integral = :integral,
							apply_num = :apply_num,
							remark = :remark,
							reject_note = '',
							state = :state,
							luu = :luu,
							lcd = :lcd
						where id = :id
						";
                break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$staffId,PDO::PARAM_STR);
		if (strpos($sql,':activity_id')!==false)
			$command->bindParam(':activity_id',$this->activity_id,PDO::PARAM_STR);
		if (strpos($sql,':set_id')!==false)
			$command->bindParam(':set_id',$this->set_id,PDO::PARAM_INT);
		if (strpos($sql,':apply_num')!==false)
			$command->bindParam(':apply_num',$this->apply_num,PDO::PARAM_INT);
		if (strpos($sql,':integral')!==false)
			$command->bindParam(':integral',$this->integral,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':state')!==false)
			$command->bindParam(':state',$this->state,PDO::PARAM_STR);

        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date("Y-m-d H:i:s"),PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='apply'||$this->scenario=='audit'){
            //扣除庫存
            Yii::app()->db->createCommand("update gr_integral_cut set inventory=inventory-".$this->apply_num." where id=".$this->set_id)->execute();
        }
        return true;
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
