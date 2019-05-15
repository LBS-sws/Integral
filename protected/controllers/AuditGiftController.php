<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class AuditGiftController extends Controller
{
	public $function_id='GA02';

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
                'actions'=>array('edit','audit','reject','test'),
                'expression'=>array('AuditGiftController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AuditGiftController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('GA02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('GA02');
    }

    public function actionIndex($pageNum=0){
        $model = new AuditGiftList;
        if (isset($_POST['AuditGiftList'])) {
            $model->attributes = $_POST['AuditGiftList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditGift_01']) && !empty($session['auditGift_01'])) {
                $criteria = $session['auditGift_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditGiftForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AuditGiftForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['AuditGiftForm'])) {
            $model = new AuditGiftForm("audit");
            $model->attributes = $_POST['AuditGiftForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditGift/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditGift/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionReject()
    {
        if (isset($_POST['AuditGiftForm'])) {
            $model = new AuditGiftForm("reject");
            $model->attributes = $_POST['AuditGiftForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditGift/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditGift/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionTest()
    {
        $model = new RptCutList();
        $model->criteria=array(
            'START_DT'=>'2018/07/19',
            'END_DT'=>'2018/07/19',
            'CITY'=>'SZ',
            'STAFFS'=>'',
        );
        $model->retrieveData();
        var_dump($model->data);
    }

}