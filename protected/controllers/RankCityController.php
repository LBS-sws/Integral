<?php

class RankCityController extends Controller
{
	public $function_id='RL01';
	
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
                'expression'=>array('RankCityController','allowReadOnly'),
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
		$model = new RankCityList;
		if(Yii::app()->user->isSingleCity()){
		    $model->city = Yii::app()->user->city();
        }
		if (isset($_POST['RankCityList'])) {
			$model->attributes = $_POST['RankCityList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankCity_op01']) && !empty($session['rankCity_op01'])) {
				$criteria = $session['rankCity_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionExport()
	{
		$model = new RankCityList;
		if (isset($_POST['RankCityList'])) {
			$model->attributes = $_POST['RankCityList'];
			if(empty($model->city)){
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('integral','city not empty'));
                $this->redirect(Yii::app()->createUrl('rankCity/index'));
            }else{
			    $model->export();
            }
		} else {
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('integral','city not empty'));
            $this->redirect(Yii::app()->createUrl('rankCity/index'));
		}
	}
}
