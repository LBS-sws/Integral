<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class GiftSearchController extends Controller
{
	public $function_id='SR03';

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
                'actions'=>array('index','view'),
                'expression'=>array('GiftSearchController','allowAddReadOnly'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('GiftSearchController','allowCancel'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowCancel() {
        return Yii::app()->user->validFunction('ZR06');
    }
    public static function allowAddReadOnly() {
        return Yii::app()->user->validFunction('SR03');
    }

    public function actionIndex($pageNum=0){
        $model = new GiftSearchList;
        if (isset($_POST['GiftSearchList'])) {
            $model->attributes = $_POST['GiftSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['giftSearch_01']) && !empty($session['giftSearch_01'])) {
                $criteria = $session['giftSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
    public function actionView($index)
    {
        $model = new GiftSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    //取消
    public function actionCancel(){
        $model = new GiftSearchForm('cancel');
        if (isset($_POST['GiftSearchForm'])) {
            $index = $_POST['GiftSearchForm']["id"];
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'该积分无法取消');
            } else {
                $model->giftCancel();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Cancel Done'));
                $this->redirect(Yii::app()->createUrl('GiftSearch/index'));
            }
        }
    }
}