<?php

class IntegralAddForm extends CFormModel
{
	public $id;
	public $integral_name;
	public $integral_num;

	public function attributeLabels()
	{
		return array(
            'integral_name'=>Yii::t('integral','Integral Name'),
            'integral_num'=>Yii::t('integral','Integral Num'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, integral_name,integral_num','safe'),
            array('integral_name','required'),
            array('integral_num','required'),
			array('integral_name','validateName'),
            array('integral_num', 'numerical', 'min'=>0, 'integerOnly'=>true),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_integral_add")
            ->where('integral_name=:integral_name and id!=:id', array(':integral_name'=>$this->integral_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("gr_integral_add")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->integral_name = $row['integral_name'];
                $this->integral_num = $row['integral_num'];
                break;
			}
		}
		return true;
	}

    //獲取積分類型列表
    public function getIntegralAddList(){
	    $arr = array(
	        ""=>array("name"=>"","num"=>"")
        );
        $rs = Yii::app()->db->createCommand()->select()->from("gr_integral_add")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] =array("name"=>$row["integral_name"],"num"=>$row["integral_num"]);
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_integral")->where("alg_con=0 and set_id=:set_id",array(":set_id"=>$this->id))->queryAll();
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
                $sql = "delete from gr_integral_add where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_integral_add(
							integral_name,integral_num, lcu, city
						) values (
							:integral_name,:integral_num, :lcu, :city
						)";
                break;
            case 'edit':
                $sql = "update gr_integral_add set
							integral_name = :integral_name, 
							integral_num = :integral_num, 
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
        if (strpos($sql,':integral_name')!==false)
            $command->bindParam(':integral_name',$this->integral_name,PDO::PARAM_STR);
        if (strpos($sql,':integral_num')!==false)
            $command->bindParam(':integral_num',$this->integral_num,PDO::PARAM_INT);

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
}
