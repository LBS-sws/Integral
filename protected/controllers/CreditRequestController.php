<?php

class CreditRequestController extends Controller
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
                'expression'=>array('CreditRequestController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','edit','view','fileDownload'),
                'expression'=>array('CreditRequestController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('importCredit'),
                'expression'=>array('CreditRequestController','allowImport'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('CreditRequestController','allowCancelled'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowCancelled() {
        return Yii::app()->user->validFunction('ZR04');
    }

    public static function allowImport() {
        return Yii::app()->user->validFunction('ZR03');
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('DE01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('DE02')||Yii::app()->user->validFunction('DE01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new CreditRequestList();
		if (isset($_POST['CreditRequestList'])) {
			$model->attributes = $_POST['CreditRequestList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['creditRequest_op01']) && !empty($session['creditRequest_op01'])) {
				$criteria = $session['creditRequest_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['CreditRequestForm'])) {
			$model = new CreditRequestForm($_POST['CreditRequestForm']['scenario']);
			$model->attributes = $_POST['CreditRequestForm'];
			if ($model->validate()) {
			    $model->state = 0;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('creditRequest/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['CreditRequestForm'])) {
			$model = new CreditRequestForm($_POST['CreditRequestForm']['scenario']);
			$model->attributes = $_POST['CreditRequestForm'];
			if ($model->validate()) {
                $model->state = 1;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('creditRequest/edit',array('index'=>$model->id)));
			} else {
                $model->state = 0;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionEdit($index){
        $model = new CreditRequestForm('edit');
        if($model->validateNowUser(true)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index){
        $model = new CreditRequestForm('view');
        if($model->validateNowUser(true)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionNew()
    {
        $model = new CreditRequestForm('new');
        if($model->validateNowUser(true)){
            $this->render('form',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }
    //刪除
    public function actionDelete(){
        $model = new CreditRequestForm('delete');
        if (isset($_POST['CreditRequestForm'])) {
            $model->attributes = $_POST['CreditRequestForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('creditRequest/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('creditRequest/index'));
    }

    public function actionFileupload($doctype) {
        $model = new CreditRequestForm();
        if (isset($_POST['CreditRequestForm'])) {
            $model->attributes = $_POST['CreditRequestForm'];

            $id = ($_POST['CreditRequestForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new CreditRequestForm();
        if (isset($_POST['CreditRequestForm'])) {
            $model->attributes = $_POST['CreditRequestForm'];

            $docman = new DocMan($model->docType,$model->id,'CreditRequestForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_credit_request where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'CreditRequestForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }


    //導入
    public function actionImportCredit(){
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
            $this->redirect(Yii::app()->createUrl('creditRequest/index'));
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadCreditRequest($list);
            $this->redirect(Yii::app()->createUrl('creditRequest/index'));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('creditRequest/index'));
        }
    }

    //取消
    public function actionCancel(){
        $model = new CreditRequestForm('cancel');
        if (isset($_POST['CreditRequestForm'])) {
            $model->attributes = $_POST['CreditRequestForm'];
            $date = $model->validateCancel();
            if($date["status"]){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Cancel Done'));
                $this->redirect(Yii::app()->createUrl('creditRequest/index'));
            }else{
                //$message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Information'), $date["message"]);
                $this->redirect(Yii::app()->createUrl('creditRequest/edit',array('index'=>$model->id)));
            }
        }
    }
}
