<?php

class IntegralCutForm extends CFormModel
{
	public $id;
	public $integral_name;
	public $integral_num;
	public $inventory;
	public $remark;


    public $no_of_attm = array(
        'icut'=>0
    );
    public $docType = 'ICUT';
    public $docMasterId = array(
        'icut'=>0
    );
    public $files;
    public $removeFileId = array(
        'icut'=>0
    );
	public function attributeLabels()
	{
        return array(
            'integral_name'=>Yii::t('integral','Cut Name'),
            'integral_num'=>Yii::t('integral','Cut Integral'),
            'inventory'=>Yii::t('integral','inventory'),
            'remark'=>Yii::t('integral','Remark'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, integral_name,integral_num,inventory,remark','safe'),
            array('integral_name','required'),
            array('integral_num','required'),
            array('inventory','required'),
			array('integral_name','validateName'),
            array('integral_num', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('inventory', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_integral_cut")
            ->where('integral_name=:integral_name and id!=:id', array(':integral_name'=>$this->integral_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('ICUT',id) as icutdoc")
            ->from("gr_integral_cut")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->integral_name = $row['integral_name'];
                $this->integral_num = $row['integral_num'];
                $this->inventory = $row['inventory'];
                $this->remark = $row['remark'];
                $this->no_of_attm['icut'] = $row['icutdoc'];
                break;
			}
		}
		return true;
	}

    //獲取積分類型列表
    public function getIntegralCutNameToId($id){
        $rs = Yii::app()->db->createCommand()->select("integral_name")->from("gr_integral_cut")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rs){
            return $rs["integral_name"];
        }
        return $id;
    }

    //獲取積分類型列表
    public function getIntegralCutListToId($id){
        $rs = Yii::app()->db->createCommand()->select("*")->from("gr_integral_cut")->where("id=:id",array(":id"=>$id))->queryAll();
        if($rs){
            return $rs;
        }
        return array();
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_gral_cut")->where("set_id=:set_id",array(":set_id"=>$this->id))->queryAll();
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
            $this->updateDocman($connection,'ICUT');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
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

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from gr_integral_cut where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_integral_cut(
							integral_name,integral_num,inventory,remark, lcu, city
						) values (
							:integral_name,:integral_num,:inventory,:remark, :lcu, :city
						)";
                break;
            case 'edit':
                $sql = "update gr_integral_cut set
							integral_name = :integral_name, 
							integral_num = :integral_num, 
							inventory = :inventory, 
							remark = :remark, 
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
        if (strpos($sql,':inventory')!==false)
            $command->bindParam(':inventory',$this->inventory,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
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
}
