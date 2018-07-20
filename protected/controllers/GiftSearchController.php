<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class GiftSearchController extends Controller
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
                'actions'=>array('index','view'),
                'expression'=>array('GiftSearchController','allowAddReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
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
}