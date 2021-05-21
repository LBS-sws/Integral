<?php

class StretchSearchController extends Controller
{
	public $function_id='SR04';

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
                'actions'=>array('index','test'),
                'expression'=>array('StretchSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR04');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new StretchSearchList;
		if (isset($_POST['StretchSearchList'])) {
			$model->attributes = $_POST['StretchSearchList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['stretchSearch_op01']) && !empty($session['stretchSearch_op01'])) {
				$criteria = $session['stretchSearch_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionTest()
	{
		$model = new ResetIntegral();
        $model->start();
	}
}
