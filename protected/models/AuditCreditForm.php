<?php

class AuditCreditForm extends CFormModel
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
    public $state = 0;//狀態 0：草稿 1：發送  2：拒絕  3：完成  4:確定
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;
    public $integral_type;
    public $rule;
    public $validity;
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
            array('id, employee_id, employee_name, audit_date, confirm_date, credit_type, credit_point, city, validity, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('reject_note','required',"on"=>"reject"),
            array('id','required',"on"=>"reject"),
        );
    }


    public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,d.validity,b.name as employee_name,b.city as s_city,d.category,d.rule,docman$suffix.countdoc('GRAL',a.id) as graldoc")
            ->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and a.state = 4 and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
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
                $this->rule = $row['rule'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['s_city'];
                $this->validity = $row['validity'];
                $this->integral_type = $row['category'];
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

        //添加學分及積分
        if($this->scenario == "audit"){
            $this->auditCredit();
        }

		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update gr_credit_request set
							state = 3, 
							audit_date = :audit_date, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update gr_credit_request set
							state = 2, 
							audit_date = :audit_date, 
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
        if (strpos($sql,':audit_date')!==false){
            $this->audit_date = date("Y-m-d H:i:s");
            $command->bindParam(':audit_date',$this->audit_date,PDO::PARAM_STR);
        }

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        //$this->sendEmail(); //後續修改，不需要發送郵件
        $this->saveCreditFlow();
		return true;
	}

    //記錄申請
    protected function saveCreditFlow(){
        Yii::app()->db->createCommand()->insert('gr_credit_flow',array(
            'credit_id'=>$this->id,
            'state_type'=>$this->scenario == "audit"?"Finish":"Confirm Reject",
            'state_remark'=>$this->scenario == "audit"?"":$this->reject_note,
            'none_info'=>0,
            'lcu'=>Yii::app()->user->id,
        ));
    }

    //發送郵件
    protected function sendEmail(){
        if($this->scenario == "audit"){
            $str = "学分审核通过";
        }else{
            $str = "学分被拒绝";
        }
        $email = new Email();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.code as employee_code,b.city as s_city")
            ->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
        $description="$str - ".$row["employee_name"];
        $subject="$str - ".$row["employee_name"];
        $message="<p>员工编号：".$row["employee_code"]."</p>";
        $message.="<p>员工姓名：".$row["employee_name"]."</p>";
        $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
        $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
        $message.="<p>学分数值：".$row["credit_point"]."</p>";
        if($this->scenario != "audit"){
            $message.="<p>拒绝原因：".$row["reject_note"]."</p>";
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->addEmailToStaffId($row["employee_id"]);
        $email->sent();
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }

    //審核通過后添加學分及積分
    private function auditCredit(){
        $remark = $this->remark;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,d.validity,b.name as employee_name,b.city as s_city,d.category")
            ->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and a.state = 4 and b.city in ($city_allow) ", array(':id'=>$this->id))->queryRow();
        if($row){
            $startDate = $row["apply_date"];
            $year = intval(date("Y",strtotime($startDate)));
            $validity = intval($row["validity"]);
            $row["city"] = $row["s_city"];
            $this->attributes = $row;
            //學分記錄
            Yii::app()->db->createCommand()->insert('gr_credit_point', array(
                'employee_id'=>$this->employee_id,
                'credit_type'=>$this->credit_type,
                'credit_point'=>$this->credit_point,
                'rec_date'=>$startDate,
                'expiry_date'=>date("Y-m-d",strtotime("$startDate + $validity year")),
                'remark'=>$remark,
                'credit_req_id'=>$this->id,
                'city'=>$this->city,
            ));
            $point_id = Yii::app()->db->getLastInsertID();
            //學分年度記錄
            if($validity>1){
                for($i = $year;$i<$year+$validity;$i++){
                    Yii::app()->db->createCommand()->insert('gr_credit_point_ex', array(
                        'employee_id'=>$this->employee_id,
                        'point_id'=>$point_id,
                        'long_type'=>$validity,
                        'year'=>$i,
                        'start_num'=>$this->credit_point,
                        'end_num'=>$this->credit_point,
                        'lcu'=>$row["lcu"]
                    ));
                }
            }else{
                Yii::app()->db->createCommand()->insert('gr_credit_point_ex', array(
                    'employee_id'=>$this->employee_id,
                    'point_id'=>$point_id,
                    'long_type'=>1,
                    'year'=>$year,
                    'start_num'=>$this->credit_point,
                    'end_num'=>$this->credit_point,
                    'lcu'=>$row["lcu"]
                ));
            }

            //積分記錄
            Yii::app()->db->createCommand()->insert('gr_bonus_point', array(
                'employee_id'=>$this->employee_id,
                'credit_type'=>$this->credit_type,
                'bonus_point'=>$this->credit_point,
                'rec_date'=>$startDate,
                'expiry_date'=>date("Y-m-d",strtotime("$startDate + 1 year")),
                'req_id'=>$this->id,
                'city'=>$this->city,
            ));
        }else{
            throw new CHttpException(404,'Cannot update.222');
            return false;
        }
    }
}
