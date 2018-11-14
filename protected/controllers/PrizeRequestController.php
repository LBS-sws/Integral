<?php

class PrizeRequestController extends Controller
{
    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('new','save','delete','audit','fileupload','fileRemove'),
                'expression'=>array('PrizeRequestController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','edit','view','ajaxStaffGift','fileDownload'),
                'expression'=>array('PrizeRequestController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('importPrize'),
                'expression'=>array('PrizeRequestController','allowImport'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('DE03');
    }

    public static function allowImport() {
        return Yii::app()->user->validFunction('ZR03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('DE03');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new PrizeRequestList();
		if (isset($_POST['PrizeRequestList'])) {
			$model->attributes = $_POST['PrizeRequestList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['prizeRequest_op01']) && !empty($session['prizeRequest_op01'])) {
				$criteria = $session['prizeRequest_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['PrizeRequestForm'])) {
			$model = new PrizeRequestForm($_POST['PrizeRequestForm']['scenario']);
			$model->attributes = $_POST['PrizeRequestForm'];
            $model->state = 0;
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('prizeRequest/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['PrizeRequestForm'])) {
			$model = new PrizeRequestForm($_POST['PrizeRequestForm']['scenario']);
			$model->attributes = $_POST['PrizeRequestForm'];
            $model->state = 1;
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('prizeRequest/edit',array('index'=>$model->id)));
			} else {
                $model->state = 0;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionEdit($index){
        $model = new PrizeRequestForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index){
        $model = new PrizeRequestForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new PrizeRequestForm('new');
        if($model->validateNowUser(true)){
            $this->render('form',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }
    //刪除
    public function actionDelete(){
        $model = new PrizeRequestForm('delete');
        if (isset($_POST['PrizeRequestForm'])) {
            $model->attributes = $_POST['PrizeRequestForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('prizeRequest/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('prizeRequest/index'));
    }

    public function actionFileupload($doctype) {
        $model = new PrizeRequestForm();
        if (isset($_POST['PrizeRequestForm'])) {
            $model->attributes = $_POST['PrizeRequestForm'];

            $id = ($_POST['PrizeRequestForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new PrizeRequestForm();
        if (isset($_POST['PrizeRequestForm'])) {
            $model->attributes = $_POST['PrizeRequestForm'];

            $docman = new DocMan($model->docType,$model->id,'PrizeRequestForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_prize_request where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'PrizeRequestForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

    //員工可用學分的異步獲取
    public function actionAjaxStaffGift(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $staff_id = $_POST["employee_id"];
            if(empty($staff_id)){
                $staff_id = -1;
            }
            $credit = PrizeRequestForm::getCreditSumToYear($staff_id);
            echo CJSON::encode(array("status"=>1,"val"=>$credit["end_num"]));
        }else{
            $this->redirect(Yii::app()->createUrl('prizeRequest/index'));
        }
    }

    //導入
    public function actionImportPrize(){
        $model = new UploadExcelForm();
        $img = CUploadedFile::getInstance($model,'file');
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
        if(empty($img)){
            Dialog::message(Yii::t('dialog','Validation Message'), "文件不能为空");
            $this->redirect(Yii::app()->createUrl('prizeRequest/index'));
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadPrizeRequest($list);
            $this->redirect(Yii::app()->createUrl('prizeRequest/index'));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('prizeRequest/index'));
        }
    }
}
