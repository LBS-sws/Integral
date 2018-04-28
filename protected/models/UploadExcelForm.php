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

	//批量導入物品
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
/*        if($list["name"] == "员工名字"){
            $rows = Yii::app()->db->createCommand()->select("id")->from("exa_title")
                ->where('name=:name',array(':name'=>$value))->queryRow();
            if($rows){
                return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."已存在");
            }
        }*/
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
}
