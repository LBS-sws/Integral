<?php

class AuditIntegralController extends Controller
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
                'expression'=>array('AuditIntegralController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('add','view'),
                'expression'=>array('AuditIntegralController','allowReadOnly'),
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

	public function actionAdd($pageNum=0)
	{
		$model = new AuditIntegralList;
		if (isset($_POST['AuditIntegralList'])) {
			$model->attributes = $_POST['AuditIntegralList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['AuditIntegral_ya01']) && !empty($session['AuditIntegral_ya01'])) {
				$criteria = $session['AuditIntegral_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionAudit()
	{
		if (isset($_POST['AuditIntegralForm'])) {
			$model = new AuditIntegralForm("audit");
			$model->attributes = $_POST['AuditIntegralForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditIntegral/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditIntegral/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['AuditIntegralForm'])) {
			$model = new AuditIntegralForm("reject");
			$model->attributes = $_POST['AuditIntegralForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('auditIntegral/add'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditIntegral/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AuditIntegralForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new AuditIntegralForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

}
