<?php

class PrizeTypeForm extends CFormModel
{
	public $id;
	public $prize_name;
	public $prize_point = 0;
	public $tries_limit = 0;
	public $limit_number = 0;
	public $leave_limit = 0;
	public $leave_number = 0;
	public $min_point = 0;
	public $full_time = 0;
	public $z_index = 0;


    public $no_of_attm = array(
        'iprize'=>0
    );
    public $docType = 'IPRIZE';
    public $docMasterId = array(
        'iprize'=>0
    );
    public $files;
    public $removeFileId = array(
        'iprize'=>0
    );
	public function attributeLabels()
	{
		return array(
            'prize_name'=>Yii::t('integral','Prize Name'),
            'prize_point'=>Yii::t('integral','Prize Point'),
            'min_point'=>Yii::t('integral','min point'),
            'tries_limit'=>Yii::t('integral','Tries Limit'),
            'leave_limit'=>Yii::t('integral','Leave Limit'),
            'full_time'=>Yii::t('integral','Full time'),
            'z_index'=>Yii::t('integral','Level'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, prize_name, min_point, prize_point, tries_limit, limit_number leave_limit, leave_number, z_index, full_time','safe'),
            array('prize_name','required'),
            array('prize_point','required'),
            array('prize_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('min_point', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('limit_number', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('tries_limit', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('full_time', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('z_index', 'numerical', 'min'=>0, 'integerOnly'=>true),
            array('prize_name','validateName'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("gr_prize_type")
            ->where('prize_name=:prize_name and id!=:id', array(':prize_name'=>$this->prize_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('integral','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('IPRIZE',id) as iprizedoc")
            ->from("gr_prize_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->prize_name = $row['prize_name'];
                $this->prize_point = $row['prize_point'];
                $this->min_point = $row['min_point'];
                $this->tries_limit = $row['tries_limit'];
                $this->limit_number = $row['limit_number'];
                $this->leave_limit = $row['leave_limit'];
                $this->leave_number = $row['leave_number'];
                $this->full_time = $row['full_time'];
                $this->z_index = $row['z_index'];
                $this->no_of_attm['iprize'] = $row['iprizedoc'];
                break;
			}
		}
		return true;
	}

	//根據id獲取獎勵名稱
	public function getTriesLimtList() {
	    return array(
            Yii::t("integral","unlimited"),
            Yii::t("integral","have limits")
        );
	}

	//根據id獲取獎勵名稱
	public function getPrizeNameToId($index) {
        $row = Yii::app()->db->createCommand()->select("prize_name")
            ->from("gr_prize_type")->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
		    return $row['prize_name'];
		}
		return $index;
	}

    //根據id獲取獎勵列表
    public function getPrizeTypeListToId($id){
        $rs = Yii::app()->db->createCommand()->select("*")->from("gr_prize_type")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rs){
            return $rs;
        }
        return array();
    }

	//獎勵列表
	public function getPrizeTypeAll() {
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("gr_prize_type")->queryAll();
        $arr = array();
		if ($rows) {
		    foreach ($rows as $row){
                $arr[$row["id"]] = $row["prize_name"];
            }
		}
		return $arr;
	}

    //獲取獎勵類型列表
    public function getPrizeTypeList(){
        $arr = array(
            ""=>array("name"=>"","num"=>"","min_point"=>"")
        );
        $rs = Yii::app()->db->createCommand()->select()->from("gr_prize_type")->order("z_index desc")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] =array("name"=>$row["prize_name"],"num"=>$row["prize_point"],"min_point"=>$row["min_point"]);
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("gr_prize")->where("prize_type=:prize_type",array(":prize_type"=>$this->id))->queryAll();
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
            $this->updateDocman($connection,'IPRIZE');
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
                $sql = "delete from gr_prize_type where id = :id";
                break;
            case 'new':
                $sql = "insert into gr_prize_type(
							prize_name, prize_point, min_point, tries_limit, limit_number leave_number, leave_limit, full_time, z_index, lcu
						) values (
							:prize_name, :prize_point, :min_point, :tries_limit, :limit_number :leave_number, :leave_limit, :full_time, :z_index, :lcu
						)";
                break;
            case 'edit':
                $sql = "update gr_prize_type set
							prize_name = :prize_name, 
							prize_point = :prize_point, 
							min_point = :min_point, 
							tries_limit = :tries_limit, 
							limit_number = :limit_number, 
							leave_limit = :leave_limit, 
							leave_number = :leave_number, 
							full_time = :full_time, 
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
        if (strpos($sql,':prize_name')!==false)
            $command->bindParam(':prize_name',$this->prize_name,PDO::PARAM_STR);
        if (strpos($sql,':prize_point')!==false)
            $command->bindParam(':prize_point',$this->prize_point,PDO::PARAM_INT);
        if (strpos($sql,':min_point')!==false)
            $command->bindParam(':min_point',$this->min_point,PDO::PARAM_INT);
        if (strpos($sql,':tries_limit')!==false)
            $command->bindParam(':tries_limit',$this->tries_limit,PDO::PARAM_INT);
        if (strpos($sql,':limit_number')!==false)
            $command->bindParam(':limit_number',$this->limit_number,PDO::PARAM_INT);
        if (strpos($sql,':leave_limit')!==false)
            $command->bindParam(':leave_limit',$this->leave_limit,PDO::PARAM_INT);
        if (strpos($sql,':leave_number')!==false)
            $command->bindParam(':leave_number',$this->leave_number,PDO::PARAM_INT);
        if (strpos($sql,':full_time')!==false)
            $command->bindParam(':full_time',$this->full_time,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

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
