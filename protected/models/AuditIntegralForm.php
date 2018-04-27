<?php

class AuditIntegralForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $alg_con = 0;
    public $set_id;
    public $integral;
    public $images_url;
    public $remark;
    public $reject_note;
    public $state = 0;
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;
    public $integral_type;
    public $s_remark;


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

    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('integral','Record ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'set_id'=>Yii::t('integral','Integral Name'),
            'integral'=>Yii::t('integral','Integral Num'),
            'remark'=>Yii::t('integral','Remark'),
            'reject_note'=>Yii::t('integral','Reject Note'),
            'city'=>Yii::t('integral','City'),
            's_remark'=>Yii::t('integral','integral conditions'),
            'integral_type'=>Yii::t('integral','integral type'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, employee_id, employee_name, alg_con, set_id, integral, images_url, remark, reject_note, reject_note, lcu, luu, lcd, lud','safe'),
            array('reject_note','required',"on"=>"reject"),
        );
    }


    public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,d.integral_type,d.s_remark,b.name as employee_name,docman$suffix.countdoc('GRAL',a.id) as graldoc")->from("gr_integral a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_integral_add d","a.set_id = d.id")
            ->where("a.id=:id and a.alg_con = 0 and a.state = 1 and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->alg_con = $row['alg_con'];
                $this->set_id = $row['set_id'];
                $this->integral = $row['integral'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->integral_type = $row['integral_type'];
                $this->s_remark = $row['s_remark'];
                $this->no_of_attm['gral'] = $row['graldoc'];
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
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

    /*  id;employee_id;employee_code;employee_name;reward_id;reward_name;reward_money;reward_goods;remark;city;*/
	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update gr_integral set
							state = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update gr_integral set
							state = 2, 
							reject_note = :reject_note, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':reject_note')!==false)
            $command->bindParam(':reject_note',$this->reject_note,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
		return true;
	}

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }
}
