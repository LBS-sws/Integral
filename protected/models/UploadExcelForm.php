<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UploadExcelForm extends CFormModel
{
	/* User Fields */
	public $file;
	public $error_list=array();
	public $start_title="";
	public $staff_id="";//員工id
	public $staff_code="";//員工編號
	public $staff_name="";//員工名字
	public $set_id="";//積分名稱id

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('file','safe'),
            array('file', 'file', 'types'=>'xlsx,xls', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
	}

	//學分導入名稱
    private function reSetIntegralID(){
        $city = Yii::app()->user->city();
	    $date = date("Y-m-d")."导入";
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_integral_add")
            ->where('integral_name=:integral_name',array(':integral_name'=>$date))->queryRow();
        if($rows){
            $this->set_id = $rows["id"];
        }else{
            Yii::app()->db->createCommand()->insert("gr_integral_add", array(
                "integral_name"=>$date,
                "integral_num"=>"0",
                "integral_type"=>"1",
                "s_remark"=>$date."专用，員工不允許申請。",
                "city"=>$city,
            ));
            $this->set_id = Yii::app()->db->getLastInsertID();
        }
    }

	//批量導入（學分配置）
    public function loadCreditType($arr){
	    $errNum = 0;//失敗條數
	    $successNum = 0;//成功條數
        $validateArr = $this->getCreditTypeList();
        foreach ($validateArr as $vaList){
            if(!in_array($vaList["name"],$arr["listHeader"])){
                Dialog::message(Yii::t('dialog','Validation Message'), $vaList["name"]."沒找到");
                return false;
            }
        }
        foreach ($arr["listBody"] as $list){
            $arrList = array();
            $continue = true;
            $this->start_title = current($list);//每行的第一個文本
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList);
                if($value['status'] == 1){
                    if($vaList["sqlName"] == "year_sw"){
                        if(!is_numeric($value["data"])){
                            $arrList["year_sw"] = 0;
                        }else{
                            $arrList["year_sw"] = 1;
                            $arrList["year_max"] = $value["data"];
                        }
                    }else{
                        $arrList[$vaList["sqlName"]] = $value["data"];
                    }
                }else{
                    $continue = false;
                    array_push($this->error_list,$value["error"]);
                    break;
                }
            }
            if($continue){
                $city = Yii::app()->user->city();
                $uid = Yii::app()->user->id;
                //新增
                $arrList["lcu"] = $uid;
                $arrList["city"] = $city;
                Yii::app()->db->createCommand()->insert("gr_credit_type", $arrList);
                $successNum++;
            }else{
                $errNum++;
            }
        }
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), "成功数量：".$successNum."<br>失败数量：".$errNum."<br>".$error);
    }

	//批量導入（學分）
    public function loadCreditRequest($arr){
	    $errNum = 0;//失敗條數
	    $successNum = 0;//成功條數
        $validateArr = $this->getCreditRequestList();
        foreach ($validateArr as $vaList){
            if(!in_array($vaList["name"],$arr["listHeader"])){
                Dialog::message(Yii::t('dialog','Validation Message'), $vaList["name"]."沒找到");
                return false;
            }
        }
        foreach ($arr["listBody"] as $list){
            $arrList = array();
            $continue = true;
            $this->start_title = current($list);//每行的第一個文本
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList);
                if($value['status'] == 1){
                    if($vaList["sqlName"] == "expiry_date"){
                        $arrHisList["expiry_date"]=$value["data"];
                    }else{
                        $arrList[$vaList["sqlName"]] = $value["data"];
                        if($vaList["sqlName"] != "apply_date"){
                            $arrHisList[$vaList["sqlName"]] = $value["data"];
                        }
                    }
                }else{
                    $continue = false;
                    array_push($this->error_list,$value["error"]);
                    break;
                }
            }
            if($continue){
                $city = Yii::app()->user->city();
                $uid = Yii::app()->user->id;
                //新增(學分申請)
                $arrList["lcu"] = $uid;
                $arrList["audit_date"] = date("Y-m-d");
                $arrList["reject_note"] = "系统导入，时间：".date("Y-m-d")."，用户id：".$uid;
                $arrList["city"] = $city;
                $arrList["state"] = 3;
                Yii::app()->db->createCommand()->insert("gr_credit_request", $arrList);
                //(學分記錄)
                $arrHisList["credit_req_id"]=Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->insert("gr_credit_point", $arrHisList);
                //所有年限學分添加
                $startYear = intval(date("Y",strtotime($arrList["apply_date"])));
                $endYear = intval(date("Y",strtotime($arrHisList["expiry_date"])));
                $point_id = Yii::app()->db->getLastInsertID();
                for ($i = $startYear;$i<=$endYear;$i++){
                    Yii::app()->db->createCommand()->insert("gr_credit_point_ex", array(
                        "employee_id"=>$arrHisList["employee_id"],
                        "long_type"=>$endYear-$startYear+1,
                        "year"=>$i,
                        "start_num"=>$arrHisList["credit_point"],
                        "end_num"=>$arrHisList["credit_point"],
                        "point_id"=>$point_id,
                    ));
                }
                //添加積分
                Yii::app()->db->createCommand()->insert('gr_bonus_point', array(
                    'employee_id'=>$arrHisList["employee_id"],
                    'credit_type'=>$arrHisList["credit_type"],
                    'bonus_point'=>$arrHisList["credit_point"],
                    'rec_date'=>date("Y-m-d",strtotime($arrList["apply_date"])),
                    'expiry_date'=>date("Y-m-d",strtotime($arrList["apply_date"]." + 1 year")),
                    'req_id'=>$arrHisList["credit_req_id"],
                    'city'=>$city,
                ));
                $successNum++;
            }else{
                $errNum++;
            }
        }
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), "成功数量：".$successNum."<br>失败数量：".$errNum."<br>".$error);
    }

	//批量導入（學分）
    public function loadGoods($arr){
	    $this->reSetIntegralID(); //獲取導入學分的id
	    $errNum = 0;//失敗條數
	    $successNum = 0;//成功條數
        $validateArr = $this->getList();
        foreach ($validateArr as $vaList){
            if(!in_array($vaList["name"],$arr["listHeader"])){
                Dialog::message(Yii::t('dialog','Validation Message'), $vaList["name"]."沒找到");
                return false;
            }
        }
        foreach ($arr["listBody"] as $list){
            $arrList = array();
            $continue = true;
            $this->start_title = current($list);//每行的第一個文本
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList);
                if($value['status'] == 1){
                    if($vaList["sqlName"] == "staff_code"){
                        $this->staff_code = $value["data"];
                    }elseif($vaList["sqlName"] == "staff_name"){
                        $this->staff_name = $value["data"];
                    }else{
                        $arrList[$vaList["sqlName"]] = $value["data"];
                    }
                }else{
                    $continue = false;
                    array_push($this->error_list,$value["error"]);
                    break;
                }
            }
            if($continue){
                if($this->validateStaff()){
                    $city = Yii::app()->user->city();
                    $uid = Yii::app()->user->id;
                    //新增
                    $arrList["lcu"] = $uid;
                    $arrList["city"] = $city;
                    $arrList["employee_id"] = $this->staff_id;
                    $arrList["set_id"] = $this->set_id;
                    $arrList["alg_con"] = 0;
                    $arrList["apply_num"] = 1;
                    $arrList["state"] = 3;
                    Yii::app()->db->createCommand()->insert("gr_integral", $arrList);
                    $successNum++;
                }else{
                    $errNum++;
                }
            }else{
                $errNum++;
            }
        }
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), "成功数量：".$successNum."<br>失败数量：".$errNum."<br>".$error);
    }

    private function validateStaff(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr$suffix.hr_employee")
            ->where('name=:name AND code=:code AND staff_status = 0',array(':name'=>$this->staff_name,":code"=>$this->staff_code))->queryRow();
        if($rows){
            $this->staff_id = $rows["id"];
            $rows = Yii::app()->db->createCommand()->select("id")->from("gr_integral")
                ->where('set_id=:set_id AND employee_id=:employee_id',array(':employee_id'=>$this->staff_id,":set_id"=>$this->set_id))->queryRow();
            if($rows){
                array_push($this->error_list,$this->staff_code."：该员工已导入过学分");
                return false;
            }else{
                return true;
            }
        }else{
            array_push($this->error_list,$this->staff_code."：沒找到員工");
            return false;
        }
    }

    private function validateStr($value,$list){
        if(empty($value)&&$list["empty"]){
            return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
        }
        if(key_exists("number",$list)){
            if ($list["number"]===true){
                if (!is_numeric($value)){
                    return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."只能是数字");
                }elseif (intval($value)!= floatval($value)){
                    return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."只能是整数");
                }
            }
        }
        if(key_exists("fun",$list)){
            $fun = $list["fun"];
            return call_user_func(array("UploadExcelForm",$fun),$value);
        }
        return array("status"=>1,"data"=>$value);
    }

//true:需要驗證
    private function getList(){
        $arr = array(
            array("name"=>"员工编号","sqlName"=>"staff_code","empty"=>true),
            array("name"=>"员工名字","sqlName"=>"staff_name","empty"=>true),
            array("name"=>"学分数值","sqlName"=>"integral","empty"=>true,"number"=>true),
            array("name"=>"备注","sqlName"=>"remark","empty"=>false),
        );
        return $arr;
    }

    private function getCreditTypeList(){
        $arr = array(
            array("name"=>"学分配置id","sqlName"=>"id","empty"=>true,"fun"=>"validateCreditTypeOnlyID"),
            array("name"=>"学分编号","sqlName"=>"credit_code","empty"=>true,"fun"=>"validateCode"),
            array("name"=>"学分名称","sqlName"=>"credit_name","empty"=>true,"fun"=>"validateName"),
            array("name"=>"学分数值","sqlName"=>"credit_point","empty"=>true,"number"=>true),
            array("name"=>"学分类型","sqlName"=>"category","empty"=>true,"fun"=>"validateCategory"),
            array("name"=>"年限限制","sqlName"=>"year_sw","empty"=>true),
            array("name"=>"得分条件","sqlName"=>"rule","empty"=>false),
            array("name"=>"备注","sqlName"=>"remark","empty"=>false),
        );
        return $arr;
    }

    private function getCreditRequestList(){
        $arr = array(
            array("name"=>"员工编号（旧）","sqlName"=>"employee_id","empty"=>true,"fun"=>"validateOldCode"),
            array("name"=>"学分配置id","sqlName"=>"credit_type","empty"=>true,"fun"=>"validateCreditTypeID"),
            array("name"=>"学分数值","sqlName"=>"credit_point","empty"=>true,"number"=>true),
            array("name"=>"申请时间","sqlName"=>"apply_date","empty"=>true,"fun"=>"validateDate"),
            array("name"=>"过期时间","sqlName"=>"expiry_date","empty"=>true,"fun"=>"validateDate"),
            array("name"=>"备注","sqlName"=>"remark","empty"=>false),
        );
        return $arr;
    }

    public function validateDate($value){
        $time = strtotime($value);
        if($time>0){
            return array("status"=>1,"data"=>date("Y-m-d H:i:s",$time));
        }else{
            return array("status"=>0,"error"=>"时间格式不正确:".$value);
        }
    }

    public function validateOldCode($value){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr$suffix.hr_employee")
            ->where('code_old=:code_old AND staff_status = 0',array(':code_old'=>$value))->queryRow();
        if(!$rows){
            return array("status"=>0,"error"=>"员工编号不存在:".$value);
        }else{
            return array("status"=>1,"data"=>$rows["id"]);
        }
    }

    public function validateCreditTypeOnlyID($value){
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('id=:id', array(':id'=>$value))->queryRow();
        if($rows){
            return array("status"=>0,"error"=>"学分配置id已存在:".$value);
        }else{
            return array("status"=>1,"data"=>$value);
        }
    }

    public function validateCreditTypeID($value){
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('id=:id', array(':id'=>$value))->queryRow();
        if($rows){
            return array("status"=>1,"data"=>$rows["id"]);
        }else{
            return array("status"=>0,"error"=>"学分配置id不存在:".$value);
        }
    }

    public function validateCode($value){
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('credit_code=:credit_code', array(':credit_code'=>$value))->queryAll();
        if(count($rows)>0){
            return array("status"=>0,"error"=>"学分编号:".$value."已存在");
        }else{
            return array("status"=>1,"data"=>$value);
        }
    }

    public function validateName($value){
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_credit_type")
            ->where('credit_name=:credit_name', array(':credit_name'=>$value))->queryAll();
        if(count($rows)>0){
            return array("status"=>0,"error"=>"学分名称:".$value."已存在");
        }else{
            return array("status"=>1,"data"=>$value);
        }
    }

    public function validateCategory($value){
        $arrList =  CreditTypeForm::getCategoryAll();
        $key = array_search($value,$arrList);
        if(empty($key)){
            return array("status"=>0,"error"=>"学分类型:".$value."不存在");
        }else{
            return array("status"=>1,"data"=>$key);
        }
    }
}
