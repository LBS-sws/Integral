<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class GiftRequestController extends Controller
{

	public $function_id='EX02';
	
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
                'actions'=>array('audit','edit','delete'),
                'expression'=>array('GiftRequestController','allowReadWrite'),
            ),
/*            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('IntegralController','allowReadOnly'),
            ),*/
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('GiftRequestController','allowAddReadOnly'),
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
        return Yii::app()->user->validFunction('EX02')||Yii::app()->user->validFunction('EX01');
    }

    public function actionIndex($pageNum=0){
        if(GiftRequestForm::validateNowUser()){
            $model = new GiftRequestList;
            if (isset($_POST['GiftRequestList'])) {
                $model->attributes = $_POST['GiftRequestList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['giftRequest_01']) && !empty($session['giftRequest_01'])) {
                    $criteria = $session['giftRequest_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $listArrIntegral = GiftList::getNowIntegral();
            $this->render('index',array('model'=>$model,'cutIntegral'=>$listArrIntegral));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new GiftRequestForm('edit');
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
        $model = new GiftRequestForm('view');
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
        if (isset($_POST['GiftRequestForm'])) {
            $model = new GiftRequestForm("audit");
            $model->attributes = $_POST['GiftRequestForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('giftRequest/edit',array('index'=>$model->id)));
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
        $model = new GiftRequestForm('delete');
        if (isset($_POST['GiftRequestForm'])) {
            $model->attributes = $_POST['GiftRequestForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('giftRequest/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('giftRequest/index'));
    }

}