<?php

class AuditPrizeForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id;
    public $employee_name;
    public $prize_type;
    public $prize_point;
    public $apply_date;
    public $remark;
    public $reject_note;
    public $state = 0;
    public $city;
    public $lcu;
    public $luu;
    public $lcd;
    public $lud;


    public $no_of_attm = array(
        'rpri'=>0
    );
    public $docType = 'RPRI';
    public $docMasterId = array(
        'rpri'=>0
    );
    public $files;
    public $removeFileId = array(
        'rpri'=>0
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
            'prize_type'=>Yii::t('integral','Prize Name'),
            'prize_point'=>Yii::t('integral','Prize Point'),
            'remark'=>Yii::t('integral','Remark'),
            'reject_note'=>Yii::t('integral','Reject Note'),
            'city'=>Yii::t('integral','City'),
            'apply_date'=>Yii::t('integral','apply for time'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, employee_id, employee_name, prize_type, credit_type, credit_point, city, validity, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('prize_type','validatePrize',"on"=>"audit"),
            array('reject_note','required',"on"=>"reject"),
            array('id','required',"on"=>"reject"),
        );
    }
    public function validatePrize($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("gr_prize_type")
            ->where("id=:id", array(':id'=>$this->prize_type))->queryRow();
        if ($rows){
            $this->prize_point = $rows["prize_point"];
            $creditList = PrizeRequestForm::getCreditSumToYear($this->employee_id);
            $prizeRow = Yii::app()->db->createCommand()->select("sum(prize_point) as prize_point")->from("gr_prize_request")
                ->where("employee_id=:employee_id and state = 1", array(':employee_id'=>$this->employee_id))->queryRow();
            $prizeNum = 0;//申請時當前用戶的總學分
            if($prizeRow){
                $prizeNum = $prizeRow["prize_point"];
            }
            $prizeNum = intval($creditList["end_num"])-intval($prizeNum);
            if($rows["tries_limit"]!=0){//判斷是否有次數限制
                $sumNum = Yii::app()->db->createCommand()->select("count(*)")->from("gr_prize_request")
                    ->where("employee_id=:employee_id and prize_type=:prize_type and state in (1,3)",
                        array(':prize_type'=>$this->prize_type,':employee_id'=>$this->employee_id))->queryScalar();
                if(intval($rows["limit_number"])<=$sumNum){
                    $message = Yii::t("integral","The number of applications for the award is").$rows["limit_number"];
                    $this->addError($attribute,$message);
                    return false;
                }
            }
            if($prizeNum<intval($rows["prize_point"])){//判斷學分是否足夠扣除
                $message = $this->employee_name.Yii::t("integral","available credits are").$prizeNum;
                $this->addError($attribute,$message);
                return false;
            }
            if ($prizeNum<intval($rows["min_point"])){//判斷學分是否滿足最小學分
                $message = Yii::t("integral","The minimum credits allowed by the award are").$rows["min_point"];
                $this->addError($attribute,$message);
                return false;
            }
            if($rows["full_time"] == 1){//申請時需要含有德智體群美5種學分
                $year = date("Y");
                $categoryList = CreditTypeForm::getCategoryAll();
                for ($i=1;$i<6;$i++){
                    $rs = Yii::app()->db->createCommand()->select("a.id")->from("gr_credit_point_ex a")
                        ->leftJoin("gr_credit_point c","a.point_id = c.id")
                        ->leftJoin("gr_credit_type b","c.credit_type = b.id")
                        ->where("a.employee_id=:employee_id and a.year='$year' and a.end_num!=0 and b.category=$i",
                            array(':employee_id'=>$this->employee_id))->queryRow();
                    if(!$rs){
                        $message = Yii::t("integral","The employee lacks a credit type:").$categoryList[$i];
                        $this->addError($attribute,$message);
                        return false;
                    }
                }
            }
        }else{
            $message = Yii::t('integral','Prize Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }


    public function retrieveData($index)
    {
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,docman$suffix.countdoc('RPRI',a.id) as rpridoc")
            ->from("gr_prize_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
        if (count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->prize_type = $row['prize_type'];
                $this->prize_point = $row['prize_point'];
                $this->apply_date = $row['apply_date'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->no_of_attm['rpri'] = $row['rpridoc'];
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

        //扣減學分
        if($this->scenario == "audit"){
            $this->auditPrize();
        }

		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update gr_prize_request set
							state = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update gr_prize_request set
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

        $this->sendEmail();
		return true;
	}

    //發送郵件
    protected function sendEmail(){
        if($this->scenario == "audit"){
            $str = "金银铜奖项申请审核通过";
        }else{
            $str = "金银铜奖项申请被拒绝";
        }
        $email = new Email();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,c.prize_name,b.name as employee_name,b.code as employee_code,b.city as s_city")
            ->from("gr_prize_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_prize_type c","a.prize_type = c.id")
            ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
        $description="$str - ".$row["employee_name"];
        $subject="$str - ".$row["employee_name"];
        $message="<p>员工编号：".$row["employee_code"]."</p>";
        $message.="<p>员工姓名：".$row["employee_name"]."</p>";
        $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
        $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
        $message.="<p>奖项名称：".$row["prize_name"]."</p>";
        $message.="<p>扣除学分：".$row["prize_point"]."</p>";
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

    //扣減學分
    private function auditPrize(){
        $remark = $this->remark;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.city as s_city")
            ->from("gr_prize_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.state = 1 and b.city in ($city_allow) ", array(':id'=>$this->id))->queryRow();
        if($row){
            $sum = $row["prize_point"];
            if(!empty($sum)){
                $sum = intval($sum);//需要扣減的總學分
                $year = date("Y",strtotime($row["apply_date"]));//申請的年份
                $creditList = Yii::app()->db->createCommand()->select("id,long_type,end_num,point_id")->from("gr_credit_point_ex")
                    ->where("employee_id=:employee_id and year=:year and end_num>0",array(":employee_id"=>$row["employee_id"],":year"=>$year))
                    ->order('long_type,lcu asc')->queryAll();
                $num = 0;//已經扣減的學分
                if($creditList){
                    foreach ($creditList as $credit){
                        $nowNum = intval($credit["end_num"]);
                        $num+=$nowNum;
                        $updateNum = $num<$sum?0:$num-$sum;
                        Yii::app()->db->createCommand()->update('gr_credit_point_ex', array(
                            'end_num'=>$updateNum,
                        ), 'id=:id', array(':id'=>$credit["id"]));
                        if(intval($credit["long_type"]) > 1){ //需要修改5年限的學分
                            Yii::app()->db->createCommand()->update('gr_credit_point_ex', array(
                                //'start_num'=>$updateNum,//總積分不應該變
                                'end_num'=>$updateNum,
                            ), 'point_id=:point_id and year > :year', array(':point_id'=>$credit["point_id"],':year'=>$year));
                        }
                        if($num>=$sum){
                            break;
                        }
                    }
                }else{
                    throw new CHttpException(404,'Cannot update.33333');
                }
            }
        }else{
            throw new CHttpException(404,'Cannot update.222');
            return false;
        }
    }
}
