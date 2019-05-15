<?php

class DeductionSearchController extends Controller
{
	public $function_id='SR06';
	
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
                'actions'=>array('index'),
                'expression'=>array('DeductionSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR06');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new DeductionSearchList;
		if (isset($_POST['DeductionSearchList'])) {
			$model->attributes = $_POST['DeductionSearchList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['deductionSearch_op01']) && !empty($session['deductionSearch_op01'])) {
				$criteria = $session['deductionSearch_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}
}
