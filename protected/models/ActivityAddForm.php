<?php

class ActivityAddForm extends CFormModel
{
	public $id;
	public $name;
	public $start_time;
	public $end_time;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('integral','Credit activities Name'),
            'start_time'=>Yii::t('integral','Start time'),
            'end_time'=>Yii::t('integral','End time'),
		);
	}


    public function init(){
        $this->start_time = date("Y/m/d");
    }
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name, start_time,end_time','safe'),
            array('name','required'),
            array('start_time','required'),
            array('end_time','required'),
            array('name','validateName'),
            array('start_time', 'date', 'format'=>'yyyy/MM/dd'),
            array('end_time', 'date', 'format'=>'yyyy/MM/dd'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_act_add")
            ->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("gr_act_add")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->start_time = CGeneral::toDate($row['start_time']);
                $this->end_time = CGeneral::toDate($row['end_time']);
                break;
			}
		}
		return true;
	}

	//根據id獲取活動名稱
	public function getActivityNameToId($index) {
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("gr_act_add")->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
		    return $row['name'];
		}
		return $index;
	}

	//活動列表
	public function getActivityAll() {
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("gr_act_add")->queryAll();
        $arr = array();
		if ($rows) {
		    foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
		}
		return $arr;
	}

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_gral_add")->where("activity_id=:activity_id",array(":activity_id"=>$this->id))->queryAll();
        if($rs0){
            return false;
        }else{
            return true;
        }
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from gr_act_add where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_act_add(
							name,start_time, end_time, lcu
						) values (
							:name,:start_time, :end_time, :lcu
						)";
                break;
            case 'edit':
                $sql = "update gr_act_add set
							name = :name, 
							start_time = :start_time, 
							end_time = :end_time, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
