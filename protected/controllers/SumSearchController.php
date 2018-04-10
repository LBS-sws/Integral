<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class SumSearchController extends Controller
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
                'expression'=>array('SumSearchController','allowReadWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validFunction('SR02');
    }

    public function actionIndex($pageNum=0){
        $model = new SumSearchList;
        if (isset($_POST['SumSearchList'])) {
            $model->attributes = $_POST['SumSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['sumSearch_01']) && !empty($session['sumSearch_01'])) {
                $criteria = $session['sumSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index){
        $list = array(
            "searchField"=>"employee_name",
            "searchValue"=>$index,
        );
        $session = Yii::app()->session;
        if (isset($session['sumSearch_01']) && !empty($session['sumSearch_01'])) {
            $criteria = $session['sumSearch_01'];
            if (isset($criteria['searchTimeStart']) && !empty($criteria['searchTimeStart'])) {
                $list["searchTimeStart"] =$criteria['searchTimeStart'];
            }
            if (isset($criteria['searchTimeEnd']) && !empty($criteria['searchTimeEnd'])) {
                $list["searchTimeEnd"] =$criteria['searchTimeEnd'];
            }
        }
        $session['integralSearch_01'] = $list;
        $this->redirect(Yii::app()->createUrl('integralSearch/index'));
    }
}