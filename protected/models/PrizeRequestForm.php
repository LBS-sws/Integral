<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class PrizeRequestForm extends CFormModel
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
			array('id, employee_id, employee_name, prize_type, prize_point, apply_date, remark, reject_note, lcu, luu, lcd, lud','safe'),

			array('employee_id','required'),
            array('employee_id','validateEmployee'),
			array('prize_type','required'),
			array('prize_type','validatePrize'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validatePrize($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("gr_prize_type")
            ->where("id=:id", array(':id'=>$this->prize_type))->queryRow();
        if ($rows){
            $this->prize_point = $rows["prize_point"];
            $creditList = $this->getCreditSumToYear($this->employee_id);
            $prizeRow = Yii::app()->db->createCommand()->select("sum(prize_point) as prize_point")->from("gr_prize_request")
                ->where("employee_id=:employee_id and state = 1", array(':employee_id'=>$this->employee_id))->queryRow();
            $prizeNum = 0;//申請時當前用戶的總學分
            if($prizeRow){
                $prizeNum = $prizeRow["prize_point"];
            }
            $prizeNum = intval($creditList["end_num"])-intval($prizeNum);
            if($this->state == 1){
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
                    $dateSql = date("Y-m-d");
                    $dateSql = date("Y-01-01",strtotime("$dateSql - 5 years"));
                    $categoryList = CreditTypeForm::getCategoryAll();
                    for ($i=1;$i<6;$i++){
                        $rs = Yii::app()->db->createCommand()->select("a.id")->from("gr_credit_request a")
                            ->leftJoin("gr_credit_type b","a.credit_type = b.id")
                            ->where("a.employee_id=:employee_id and a.state = 3 and a.apply_date>='$dateSql' and b.category=$i",
                                array(':employee_id'=>$this->employee_id))->queryRow();
                        if(!$rs){
                            $message = Yii::t("integral","The employee lacks a credit type:").$categoryList[$i];
                            $this->addError($attribute,$message);
                            return false;
                        }
                    }
                }
            }
        }else{
            $message = Yii::t('integral','Prize Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //驗證當前用戶的權限
    public function validateNowUser($bool = false){
        if(Yii::app()->user->validFunction('ZR01')){
            return true;//允許待申請
        }else{
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

	public function validateEmployee($attribute, $params){
        $suffix = Yii::app()->params['envSuffix'];
        $from = "hr".$suffix.".hr_employee";
        $city_allow = Yii::app()->user->city_allow();
        $sql="";
        if(Yii::app()->user->validFunction('ZR01')){//允許代申請
            $sql = " and city in ($city_allow)";
        }else{
            $this->employee_id = Yii::app()->user->staff_id();//
        }
        $rows = Yii::app()->db->createCommand()->select("name,city")->from($from)
            ->where("id=:id $sql and staff_status=0 ", array(':id'=>$this->employee_id))->queryRow();
        if ($rows){
            $this->employee_name = $rows["name"];
            $this->city = $rows["city"];
        }else{
            $message = Yii::t('integral','Employee Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //獎金删除
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("gr_prize_request")
            ->where('id=:id and state in (0,2)', array(':id'=>$this->id))->queryRow();
        if ($rows){
            return true; //允許刪除
        }
        return false;
    }

    //獎金退回
	public function backPrize(){
        if(!Yii::app()->user->validFunction('ZR05')){//沒有權限
            return false;
        }
        $row = Yii::app()->db->createCommand()->select()->from("gr_prize_request")
            ->where('id=:id and state = 3', array(':id'=>$this->id))->queryRow();
        if ($row){
            $sum = $row["prize_point"];
            if(!empty($sum)){
                $sum = intval($sum);//需要扣減的總學分
                $year = date("Y",strtotime($row["apply_date"]));//申請的年份
                $creditList = Yii::app()->db->createCommand()->select("id,long_type,start_num,end_num,point_id")->from("gr_credit_point_ex")
                    ->where("employee_id=:employee_id and year=:year and end_num<start_num",array(":employee_id"=>$row["employee_id"],":year"=>$year))
                    ->order('long_type,lcu asc')->queryAll();
                $num = 0;//已經退回的學分
                if($creditList){
                    foreach ($creditList as $credit){
                        $nowNum = intval($credit["start_num"])-intval($credit["end_num"]);
                        if($nowNum<=$sum-$num){
                            $updateNum=$nowNum;
                        }else{
                            $updateNum=$sum-$num;
                        }
                        $num+=$updateNum;
                        $updateNum+=intval($credit["end_num"]);
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
                    if($num<$sum){
                        //異常
                    }
                }else{
                    return false;
                }
            }
            Yii::app()->db->createCommand()->update('gr_prize_request', array(
                'state'=>0,
                //'reject_note'=>$this->reject_note,
            ), 'id=:id', array(':id'=>$this->id));
            return true; //允許退回
        }
        return false;
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
			$this->saveStaff($connection);
            $this->updateDocman($connection,'RPRI');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
            $this->scenario = "edit";
        }
    }

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from gr_prize_request where id = :id and city IN ($city_allow)";
				break;
			case 'new':
				$sql = "insert into gr_prize_request(
							employee_id, apply_date, prize_type, prize_point, remark, state, city, lcu
						) values (
							:employee_id, :apply_date, :prize_type, :prize_point, :remark, :state, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update gr_prize_request set
							employee_id = :employee_id, 
							apply_date = :apply_date, 
							prize_type = :prize_type, 
							prize_point = :prize_point,
							remark = :remark,
							reject_note = '',
							state = :state,
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':apply_date')!==false)
			$command->bindParam(':apply_date',date("Y-m-d H:i:s"),PDO::PARAM_STR);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':prize_type')!==false)
			$command->bindParam(':prize_type',$this->prize_type,PDO::PARAM_INT);
		if (strpos($sql,':prize_point')!==false)
			$command->bindParam(':prize_point',$this->prize_point,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':state')!==false)
			$command->bindParam(':state',$this->state,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }
        $this->sendEmail();
        return true;
	}

    //發送郵件
    protected function sendEmail(){
        if($this->state == 1){
            $email = new Email();
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("a.*,c.prize_name,b.name as employee_name,b.code as employee_code,b.city as s_city")
                ->from("gr_prize_request a")
                ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
                ->leftJoin("gr_prize_type c","a.prize_type = c.id")
                ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
            $description="金银铜奖项申请 - ".$row["employee_name"];
            $subject="金银铜奖项申请 - ".$row["employee_name"];
            $message="<p>员工编号：".$row["employee_code"]."</p>";
            $message.="<p>员工姓名：".$row["employee_name"]."</p>";
            $message.="<p>员工城市：".CGeneral::getCityName($row["s_city"])."</p>";
            $message.="<p>申请时间：".CGeneral::toDate($row["apply_date"])."</p>";
            $message.="<p>奖项名称：".$row["prize_name"]."</p>";
            $message.="<p>奖项扣减学分数值：".$row["prize_point"]."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("GA03",$row["s_city"]);
            $email->sent();
        }
    }

	//獲取某員工的某年度的總學分
    public function getCreditSumToYear($employee_id="",$year=""){
	    if(empty($employee_id)){
            $employee_id = Yii::app()->user->staff_id();
        }
	    if(empty($year)){
            $year = date("Y");
        }
        $rows = Yii::app()->db->createCommand()->select("sum(start_num) as start_num,sum(end_num) as end_num")->from("gr_credit_point_ex")
            ->where("employee_id=:employee_id and year=:year", array(':employee_id'=>$employee_id,':year'=>$year))->queryRow();
	    if($rows){
	        return $rows;
        }else{
	        return array(
	            "start_num"=>0,
	            "end_num"=>0,
            );
        }
    }
}
