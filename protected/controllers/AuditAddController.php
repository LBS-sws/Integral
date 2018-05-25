<?php

class AuditAddController extends Controller
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
                'actions'=>array('edit','reject','audit'),
                'expression'=>array('AuditAddController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','FileDownload'),
                'expression'=>array('AuditAddController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('GA01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('GA01');
    }

	public function actionIndex($pageNum=0)
	{
		$model = new AuditAddList;
		if (isset($_POST['AuditAddList'])) {
			$model->attributes = $_POST['AuditAddList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['AuditAdd_ya01']) && !empty($session['AuditAdd_ya01'])) {
				$criteria = $session['AuditAdd_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionAudit()
	{
		if (isset($_POST['AuditAddForm'])) {
			$model = new AuditAddForm("audit");
			$model->attributes = $_POST['AuditAddForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditAdd/index',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditAdd/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['AuditAddForm'])) {
			$model = new AuditAddForm("reject");
			$model->attributes = $_POST['AuditAddForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditAdd/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditAdd/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AuditAddForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new AuditAddForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}


    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_gral_add where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'IntegralForm');
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
