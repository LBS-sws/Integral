<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class CutSearchController extends Controller
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
                'expression'=>array('CutSearchController','allowReadWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validFunction('SR03');
    }

    public function actionIndex($pageNum=0){
        $model = new CutSearchList;
        if (isset($_POST['CutSearchList'])) {
            $model->attributes = $_POST['CutSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['cutSearch_01']) && !empty($session['cutSearch_01'])) {
                $criteria = $session['cutSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }
    public function actionView($index)
    {
        $model = new CutSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
}