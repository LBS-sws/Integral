<?php

class ConfirmCreditController extends Controller
{
	public $function_id='GA04';

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
                'expression'=>array('ConfirmCreditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','FileDownload'),
                'expression'=>array('ConfirmCreditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('GA04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('GA04');
    }

	public function actionIndex($pageNum=0)
	{
		$model = new ConfirmCreditList;
		if (isset($_POST['ConfirmCreditList'])) {
			$model->attributes = $_POST['ConfirmCreditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['confirmCredit_ya01']) && !empty($session['confirmCredit_ya01'])) {
				$criteria = $session['confirmCredit_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionAudit()
	{
		if (isset($_POST['ConfirmCreditForm'])) {
			$model = new ConfirmCreditForm("audit");
			$model->attributes = $_POST['ConfirmCreditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('confirmCredit/index',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('confirmCredit/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['ConfirmCreditForm'])) {
			$model = new ConfirmCreditForm("reject");
			$model->attributes = $_POST['ConfirmCreditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('confirmCredit/index'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('confirmCredit/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ConfirmCreditForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new ConfirmCreditForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
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
}
