<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class CutController extends Controller
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
                'actions'=>array('audit','edit','delete','apply'),
                'expression'=>array('CutController','allowReadWrite'),
            ),
/*            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('IntegralController','allowReadOnly'),
            ),*/
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('CutController','allowAddReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EX01');
    }

    public static function allowAddReadOnly() {
        return Yii::app()->user->validFunction('EX02');
    }

    public function actionIndex($pageNum=0){
        if(CutForm::validateNowUser()){
            $model = new CutList;
            if (isset($_POST['CutList'])) {
                $model->attributes = $_POST['CutList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['cut_01']) && !empty($session['cut_01'])) {
                    $criteria = $session['cut_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $cutIntegral = IntegralCutView::getNowIntegral();
            $this->render('index',array('model'=>$model,'cutIntegral'=>$cutIntegral));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new CutForm('edit');
        if($model->validateNowUser()){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index)
    {
        $model = new CutForm('view');
        if($model->validateNowUser()){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['CutForm'])) {
            $model = new CutForm("audit");
            $model->attributes = $_POST['CutForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('cut/edit',array('index'=>$model->id)));
            } else {
                $model->state = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new CutForm('delete');
        if (isset($_POST['CutForm'])) {
            $model->attributes = $_POST['CutForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('cut/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('cut/index'));
    }

}