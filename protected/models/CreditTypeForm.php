<?php

class CreditTypeForm extends CFormModel
{
	public $id;
	public $credit_code;
	public $credit_name;
	public $credit_point;
	public $category;
	public $rule;
	public $z_index=0;
	public $year_sw=0;
	public $year_max=0;
	public $validity=5;
	public $remark;
	public $display=1;

	public function attributeLabels()
	{
		return array(
            'credit_code'=>Yii::t('integral','Integral Code'),
            'credit_name'=>Yii::t('integral','Integral Name'),
            'credit_point'=>Yii::t('integral','Integral Num'),
            'rule'=>Yii::t('integral','integral conditions'),
            'category'=>Yii::t('integral','integral type'),
            'year_sw'=>Yii::t('integral','Age limit'),
            'year_max'=>Yii::t('integral','Limited number'),
            'z_index'=>Yii::t('integral','Level'),
            'remark'=>Yii::t('integral','Remark'),
            'display'=>Yii::t('integral','judge for visible'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,credit_code,credit_name,credit_point,category,rule,year_sw,year_max,remark,z_index,display','safe'),
            array('credit_code','required'),
            array('credit_name','required'),
            array('credit_point','required'),
            array('category','required'),
            array('year_sw','required'),
            array('year_max','validateYearNum'),
            array('year_sw', 'in', 'range' => array(0, 1)),
            array('credit_name','validateName'),
            array('credit_code','validateCode'),
            array('credit_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('z_index', 'numerical', 'integerOnly'=>true),
		);
	}

	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('credit_code=:credit_code and id!=:id', array(':credit_code'=>$this->credit_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the code of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('credit_name=:credit_name and id!=:id', array(':credit_name'=>$this->credit_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function validateYearNum($attribute, $params){
	    if($this->year_sw == 1){
	        if(!is_numeric($this->year_max)){
                $message = "限制次数只能为数字";
                $this->addError($attribute,$message);
            }else{
	            if(intval($this->year_max)!=floatval($this->year_max)){
                    $message = "限制次数只能为整數";
                    $this->addError($attribute,$message);
                }
            }
        }
	}

	public function getCategoryAll(){
        return array(
            ''=>'',
            1=>Yii::t("integral","de"),
            2=>Yii::t("integral","wisdom"),
            3=>Yii::t("integral","the body"),
            4=>Yii::t("integral","group of"),
            5=>Yii::t("integral","beauty"),
            6=>Yii::t("integral","2014 year Credit"),
        );
    }

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("gr_credit_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->credit_code = $row['credit_code'];
                $this->credit_name = $row['credit_name'];
                $this->credit_point = $row['credit_point'];
                $this->category = $row['category'];
                $this->rule = $row['rule'];
                $this->year_sw = $row['year_sw'];
                $this->year_max = $row['year_max'];
                $this->z_index = $row['z_index'];
                $this->validity = $row['validity'];
                $this->remark = $row['remark'];
                $this->display = $row['display'];
                break;
			}
		}
		return true;
	}

    //獲取積分類型列表
    public function getCreditTypeList($ready=true){
	    $arr = array(
	        ""=>array("name"=>"","num"=>"","gral"=>"")
        );
	    if($ready){
            $where = "credit_point>0";
        }else{
            $where = "credit_point>0 and display=1";
        }
        $rs = Yii::app()->db->createCommand()->select()->from("gr_credit_type")->where($where)->order("z_index desc")->queryAll();
        if($rs){
            foreach ($rs as $row){//&amp;nbsp;
                $arr[$row["id"]] =array("name"=>$row["credit_code"]." - ".$row["credit_name"],"num"=>$row["credit_point"],"gral"=>$row["category"]);
            }
        }
        return $arr;
    }

    //獲取積分類型詳情
    public function getCreditTypeListToCreditType($creditType=""){
        $arr = array();
        if(!empty($creditType)){
            $rs = Yii::app()->db->createCommand()->select()->from("gr_credit_type")->where("id=:id",array(":id"=>$creditType))->queryRow();
            if($rs){
                $arr = $rs;
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_credit_point")->where("credit_type=:credit_type",array(":credit_type"=>$this->id))->queryAll();
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
                $sql = "delete from gr_credit_type where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_credit_type(
							credit_name,credit_code,credit_point, category, rule, remark, year_sw, year_max, validity, display, z_index, lcu, city
						) values (
							:credit_name,:credit_code,:credit_point, :category, :rule, :remark, :year_sw, :year_max, 5, :display, :z_index, :lcu, :city
						)";
                break;
            case 'edit':
                $sql = "update gr_credit_type set
							credit_name = :credit_name, 
							credit_code = :credit_code, 
							credit_point = :credit_point, 
							category = :category, 
							rule = :rule, 
							remark = :remark, 
							year_sw = :year_sw, 
							year_max = :year_max, 
							display = :display, 
							validity = 5, 
							z_index = :z_index, 
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
        if (strpos($sql,':credit_code')!==false)
            $command->bindParam(':credit_code',$this->credit_code,PDO::PARAM_STR);
        if (strpos($sql,':credit_name')!==false)
            $command->bindParam(':credit_name',$this->credit_name,PDO::PARAM_STR);
        if (strpos($sql,':category')!==false)
            $command->bindParam(':category',$this->category,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':rule')!==false)
            $command->bindParam(':rule',$this->rule,PDO::PARAM_STR);
        if (strpos($sql,':credit_point')!==false)
            $command->bindParam(':credit_point',$this->credit_point,PDO::PARAM_INT);
        if (strpos($sql,':year_sw')!==false)
            $command->bindParam(':year_sw',$this->year_sw,PDO::PARAM_INT);
        if (strpos($sql,':year_max')!==false)
            $command->bindParam(':year_max',$this->year_max,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
        if (strpos($sql,':display')!==false)
            $command->bindParam(':display',$this->display,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
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

    private function lenStr(){
        $code = strval($this->id);
        $this->credit_code = "C";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->credit_code.="0";
        }
        $this->credit_code .= $code;
    }
}
