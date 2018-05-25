<?php

class AddIntegralController extends Controller
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
                'actions'=>array('new','save','audit','fileupload','fileRemove'),
                'expression'=>array('AddIntegralController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','fileDownload'),
                'expression'=>array('AddIntegralController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('DE01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('DE01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new AddIntegralList();
		if (isset($_POST['AddIntegralList'])) {
			$model->attributes = $_POST['AddIntegralList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['addIntegral_op01']) && !empty($session['addIntegral_op01'])) {
				$criteria = $session['addIntegral_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['AddIntegralForm'])) {
			$model = new AddIntegralForm("new");
			$model->attributes = $_POST['AddIntegralForm'];
			if ($model->validate()) {
			    $model->state = 0;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('integral/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['AddIntegralForm'])) {
			$model = new AddIntegralForm("new");
			$model->attributes = $_POST['AddIntegralForm'];
			if ($model->validate()) {
                $model->state = 1;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('integral/edit',array('index'=>$model->id)));
			} else {
                $model->state = 0;
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionNew($index)
    {
        if(IntegralForm::validateNowUser()){
            $model = new AddIntegralForm('new');
            $model->activity_id = $index;
            $this->render('form',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionFileupload($doctype) {
        $model = new AddIntegralForm();
        if (isset($_POST['AddIntegralForm'])) {
            $model->attributes = $_POST['AddIntegralForm'];

            $id = ($_POST['AddIntegralForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new AddIntegralForm();
        if (isset($_POST['AddIntegralForm'])) {
            $model->attributes = $_POST['AddIntegralForm'];

            $docman = new DocMan($model->docType,$model->id,'AddIntegralForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_gral_add where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'AddIntegralForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}
