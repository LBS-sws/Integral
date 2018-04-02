<?php

class IntegralCutController extends Controller
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
                'actions'=>array('new','edit','delete','save','fileupload','fileRemove'),
                'expression'=>array('IntegralCutController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','apply','fileDownload'),
                'expression'=>array('IntegralCutController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EX01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EX01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new IntegralCutList;
		if (isset($_POST['IntegralCutList'])) {
			$model->attributes = $_POST['IntegralCutList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['integralCut_op01']) && !empty($session['integralCut_op01'])) {
				$criteria = $session['integralCut_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['IntegralCutForm'])) {
			$model = new IntegralCutForm($_POST['IntegralCutForm']['scenario']);
			$model->attributes = $_POST['IntegralCutForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('integralCut/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionApply()
	{
		if (isset($_POST['integralCutForm'])) {
			$model = new CutForm('apply');
			$model->attributes = $_POST['integralCutForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('integral','The application is successful, please wait for approval.'));
				$this->redirect(Yii::app()->createUrl('integralCut/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('integralCut/index'));
			}
		}
	}

	public function actionView($index)
	{
		$model = new IntegralCutForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new IntegralCutForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new IntegralCutForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new IntegralCutForm('delete');
        if (isset($_POST['IntegralCutForm'])) {
            $model->attributes = $_POST['IntegralCutForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('integralCut/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("dialog","This record is already in use"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('integralCut/index'));
        }
    }


    public function actionFileupload($doctype) {
        $model = new IntegralCutForm();
        if (isset($_POST['IntegralCutForm'])) {
            $model->attributes = $_POST['IntegralCutForm'];

            $id = ($_POST['IntegralCutForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new IntegralCutForm();
        if (isset($_POST['IntegralCutForm'])) {
            $model->attributes = $_POST['IntegralCutForm'];

            $docman = new DocMan($model->docType,$model->id,'IntegralCutForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_integral_cut where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'IntegralCutForm');
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
