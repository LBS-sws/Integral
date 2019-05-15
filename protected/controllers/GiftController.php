<?php

class GiftController extends Controller
{
	public $function_id='EX01';
	
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
                'actions'=>array('apply'),
                'expression'=>array('GiftController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','edit','FileDownload'),
                'expression'=>array('GiftController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EX01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EX01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new GiftList();
		if (isset($_POST['GiftList'])) {
			$model->attributes = $_POST['GiftList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['gift_op01']) && !empty($session['gift_op01'])) {
				$criteria = $session['gift_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
/*        $cutIntegral = IntegralCutView::getNowIntegral();
		$this->render('index',array('model'=>$model,'cutIntegral'=>$cutIntegral));*/
		$this->render('index',array('model'=>$model));
	}

    public function actionEdit($index)
    {
        $model = new GiftTypeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model));
        }
    }

	public function actionApply()
	{
        $model = new GiftRequestForm("apply");
        if($model->validateNowUser(true)){
            if (isset($_POST['GiftRequestForm'])) {
                $model->attributes = $_POST['GiftRequestForm'];
                if ($model->validate()) {
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('gift/index'));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->redirect(Yii::app()->createUrl('gift/index'));
                }
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
	}


    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_gift_type where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'GiftTypeForm');
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
