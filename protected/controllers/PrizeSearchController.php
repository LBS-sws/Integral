<?php

class PrizeSearchController extends Controller
{
	public $function_id='SR07';
	
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
                'actions'=>array('index','edit','view'),
                'expression'=>array('PrizeSearchController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','edit','view','FileDownload'),
                'expression'=>array('PrizeSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SR07');
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR07');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new PrizeSearchList();
		if (isset($_POST['PrizeSearchList'])) {
			$model->attributes = $_POST['PrizeSearchList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['prizeSearch_op01']) && !empty($session['prizeSearch_op01'])) {
				$criteria = $session['prizeSearch_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

    public function actionEdit($index){
        $model = new PrizeSearchForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index){
        $model = new PrizeSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_prize_request where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'PrizeRequestForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

}
