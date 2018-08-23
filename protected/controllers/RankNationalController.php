<?php

class RankNationalController extends Controller
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
                'actions'=>array('index','export'),
                'expression'=>array('RankNationalController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RL01');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new RankNationalList;
		if (isset($_POST['RankNationalList'])) {
			$model->attributes = $_POST['RankNationalList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankNational_op01']) && !empty($session['rankNational_op01'])) {
				$criteria = $session['rankNational_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionExport()
	{
		$model = new RankNationalList;
		if (isset($_POST['RankNationalList'])) {
			$model->attributes = $_POST['RankNationalList'];
            $model->export();
		} else {
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('integral','city not empty'));
            $this->redirect(Yii::app()->createUrl('rankNational/index'));
		}
	}
}
