<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CreditRequestForm extends CFormModel
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
            's_remark'=>Yii::t('integral','integral conditions'),
            'integral_type'=>Yii::t('integral','integral type'),
            'apply_date'=>Yii::t('integral','apply for time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id, employee_name, credit_type, credit_point, apply_date, images_url, remark, reject_note, lcu, luu, lcd, lud','safe'),

			array('apply_date','required'),
			array('employee_id','required'),
            array('employee_id','validateEmployee'),
			array('credit_type','required'),
			array('credit_type','validateIntegral'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateIntegral($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("*")->from("gr_credit_type")
            ->where("id=:id", array(':id'=>$this->credit_type))->queryRow();
        if ($rows){
            if($rows["year_sw"]==1){
                $count = Yii::app()->db->createCommand()->select("count(*)")->from("gr_credit_request")
                    ->where("credit_type=:credit_type and employee_id=:employee_id and state in (1,3)",
                        array(':credit_type'=>$this->credit_type,':employee_id'=>$this->employee_id))->queryScalar();
                if($count > $rows["year_max"]){
                    $message = "该学分每年申請次數不能大于".$rows["year_max"];
                    $this->addError($attribute,$message);
                }
            }else{
                $this->credit_point = $rows["credit_point"];
            }
        }else{
            $message = Yii::t('integral','Integral Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function validateEmployee($attribute, $params){
        $suffix = Yii::app()->params['envSuffix'];
        $from = "hr".$suffix.".hr_employee";
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("name,city")->from($from)
            ->where("id=:id and city in ($city_allow) and staff_status=0 ", array(':id'=>$this->employee_id))->queryRow();
        if ($rows){
            $this->city = $rows["city"];
        }else{
            $message = Yii::t('integral','Employee Name'). Yii::t('integral',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    //獲取所有已绑定员工的账号id
	public function getBindingIdList(){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $bindList = Yii::app()->db->createCommand()->select("a.user_id")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city in ($city_allow)")->queryAll();
        if($bindList){
            return array_column($bindList,"user_id");
        }
        return array();
    }

    //獲取所有已绑定员工的账号列表
	public function getBindingList(){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $bindList = Yii::app()->db->createCommand()->select("b.id,b.name")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city in ($city_allow)")->queryAll();
        $arr = array(""=>"");
        if($bindList){
            foreach ($bindList as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //积分删除
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("gr_credit_request")
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
        $rows = Yii::app()->db->createCommand()->select("a.*,d.category,d.remark as s_remark,docman$suffix.countdoc('GRAL',a.id) as graldoc")
            ->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("gr_credit_type d","a.credit_type = d.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
                $this->credit_type = $row['credit_type'];
                $this->credit_point = $row['credit_point'];
                $this->apply_date = $row['apply_date'];
                $this->images_url = $row['images_url'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->integral_type = $row['category'];
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
			$this->saveStaff($connection);
            $this->updateDocman($connection,'GRAL');
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
                $sql = "delete from gr_credit_request where id = :id and city IN ($city_allow)";
				break;
			case 'new':
				$sql = "insert into gr_credit_request(
							employee_id, apply_date, credit_type, credit_point, remark, state, city, lcu
						) values (
							:employee_id, :apply_date, :credit_type, :credit_point, :remark, :state, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update gr_credit_request set
							employee_id = :employee_id, 
							apply_date = :apply_date, 
							credit_type = :credit_type, 
							credit_point = :credit_point,
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
			$command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':credit_type')!==false)
			$command->bindParam(':credit_type',$this->credit_type,PDO::PARAM_INT);
		if (strpos($sql,':credit_point')!==false)
			$command->bindParam(':credit_point',$this->credit_point,PDO::PARAM_STR);
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
        return true;
	}

    //驗證當前用戶的權限
	public function validateNowUser($bool = false){
	    if(Yii::app()->user->validFunction('ZR01')){
	        return true;//允許待申請學分
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

//获取地区編號（模糊查詢）
    public function getCityCodeSqlLikeName($code)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code")->from($from)->where(array('like', 'name', "%$code%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["code"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }
}
