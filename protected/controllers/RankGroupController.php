<?php

class RankGroupController extends Controller
{
	public $function_id='RL03';
	
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
                'expression'=>array('RankGroupController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RL03');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new RankGroupList;
		if (isset($_POST['RankGroupList'])) {
			$model->attributes = $_POST['RankGroupList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankGroup_op01']) && !empty($session['rankGroup_op01'])) {
				$criteria = $session['rankGroup_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionExport()
	{
		$model = new RankGroupList;
		if (isset($_POST['RankGroupList'])) {
			$model->attributes = $_POST['RankGroupList'];
			if(empty($model->category)){
                Dialog::message(Yii::t('dialog','Validation Message'), '学分类型不能为空');
                $this->redirect(Yii::app()->createUrl('rankGroup/index'));
            }else{
			    $model->export();
            }
		} else {
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('integral','city not empty'));
            $this->redirect(Yii::app()->createUrl('rankGroup/index'));
		}
	}
}
