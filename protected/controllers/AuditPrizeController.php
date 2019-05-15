<?php

class AuditPrizeController extends Controller
{
	public $function_id='GA03';

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
                'expression'=>array('AuditPrizeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','FileDownload'),
                'expression'=>array('AuditPrizeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('GA03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('GA03');
    }

    public function actionIndex($pageNum=0)
    {
        $model = new AuditPrizeList;
        if (isset($_POST['AuditPrizeList'])) {
            $model->attributes = $_POST['AuditPrizeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditPrize_ya01']) && !empty($session['auditPrize_ya01'])) {
                $criteria = $session['auditPrize_ya01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionAudit()
    {
        if (isset($_POST['AuditPrizeForm'])) {
            $model = new AuditPrizeForm("audit");
            $model->attributes = $_POST['AuditPrizeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditPrize/index',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditPrize/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionReject()
    {
        if (isset($_POST['AuditPrizeForm'])) {
            $model = new AuditPrizeForm("reject");
            $model->attributes = $_POST['AuditPrizeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditPrize/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditPrize/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionView($index)
    {
        $model = new AuditPrizeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new AuditPrizeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_prize_request where id = $docId";
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
