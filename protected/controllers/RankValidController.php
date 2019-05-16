<?php

class RankValidController extends Controller
{
	public $function_id='RL04';
	
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
                'actions'=>array('index','export'),
                'expression'=>array('RankValidController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RL04');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new RankValidList;
		if (isset($_POST['RankValidList'])) {
			$model->attributes = $_POST['RankValidList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankValid_op01']) && !empty($session['rankValid_op01'])) {
				$criteria = $session['rankValid_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionExport()
	{
		$model = new RankValidList;
		if (isset($_POST['RankValidList'])) {
			$model->attributes = $_POST['RankValidList'];
            $model->export();
		} else {
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('integral','city not empty'));
            $this->redirect(Yii::app()->createUrl('rankValid/index'));
		}
	}
}
