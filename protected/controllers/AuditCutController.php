<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class AuditCutController extends Controller
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
                'actions'=>array('edit','audit','reject','test'),
                'expression'=>array('AuditCutController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AuditCutController','allowReadOnly'),
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
        $model = new AuditCutList;
        if (isset($_POST['AuditCutList'])) {
            $model->attributes = $_POST['AuditCutList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditcut_01']) && !empty($session['auditcut_01'])) {
                $criteria = $session['auditcut_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditCutForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AuditCutForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['AuditCutForm'])) {
            $model = new AuditCutForm("audit");
            $model->attributes = $_POST['AuditCutForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditCut/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditCut/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionReject()
    {
        if (isset($_POST['AuditCutForm'])) {
            $model = new AuditCutForm("reject");
            $model->attributes = $_POST['AuditCutForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditCut/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditCut/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionTest()
    {
        $model = new RptYearList();
        $model->criteria=array(
            'YEAR'=>'2018',
            'CITY'=>'SH',
            'STAFFS'=>'',
        );
        $model->retrieveData();
        var_dump($model->data);
    }

}