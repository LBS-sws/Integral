<?php

class GiftTypeForm extends CFormModel
{
	public $id;
	public $gift_name;
	public $bonus_point;
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
            'gift_name'=>Yii::t('integral','Cut Name'),
            'bonus_point'=>Yii::t('integral','Cut Integral'),
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
			array('id, gift_name,bonus_point,inventory,remark','safe'),
            array('gift_name','required'),
            array('bonus_point','required'),
            array('inventory','required'),
			array('gift_name','validateName'),
            array('bonus_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('inventory', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_gift_type")
            ->where('gift_name=:gift_name and id!=:id', array(':gift_name'=>$this->gift_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('ICUT',id) as icutdoc")
            ->from("gr_gift_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->gift_name = $row['gift_name'];
                $this->bonus_point = $row['bonus_point'];
                $this->inventory = $row['inventory'];
                $this->remark = $row['remark'];
                $this->no_of_attm['icut'] = $row['icutdoc'];
                break;
			}
		}
		return true;
	}

    //獲取兌換類型名稱
    public function getGiftTypeNameToId($id){
        $rs = Yii::app()->db->createCommand()->select("gift_name")->from("gr_gift_type")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rs){
            return $rs["gift_name"];
        }
        return $id;
    }

    //獲取兌換類型列表
    public function getGiftTypeListToId($id){
        $rs = Yii::app()->db->createCommand()->select("*")->from("gr_gift_type")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rs){
            return $rs;
        }
        return array();
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_gift_request")->where("gift_type=:gift_type",array(":gift_type"=>$this->id))->queryAll();
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
                $sql = "delete from gr_gift_type where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_gift_type(
							gift_name,bonus_point,inventory,remark, lcu, city
						) values (
							:gift_name,:bonus_point,:inventory,:remark, :lcu, :city
						)";
                break;
            case 'edit':
                $sql = "update gr_gift_type set
							gift_name = :gift_name, 
							bonus_point = :bonus_point, 
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
        if (strpos($sql,':gift_name')!==false)
            $command->bindParam(':gift_name',$this->gift_name,PDO::PARAM_STR);
        if (strpos($sql,':bonus_point')!==false)
            $command->bindParam(':bonus_point',$this->bonus_point,PDO::PARAM_INT);
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
