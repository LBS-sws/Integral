<?php

class CutIntegralController extends Controller
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
                'actions'=>array('apply'),
                'expression'=>array('CutIntegralController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','edit','FileDownload'),
                'expression'=>array('CutIntegralController','allowReadOnly'),
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
		$model = new CutIntegralList();
		if (isset($_POST['CutIntegralList'])) {
			$model->attributes = $_POST['CutIntegralList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['cutIntegral_op01']) && !empty($session['cutIntegral_op01'])) {
				$criteria = $session['cutIntegral_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
        $cutIntegral = IntegralCutView::getNowIntegral();
		$this->render('index',array('model'=>$model,'cutIntegral'=>$cutIntegral));
	}
	public function actionView($pageNum=0,$index=0)
	{
		$model = new IntegralCutView();
		$model->activity_id = $index;
		if (isset($_POST['CutIntegralList'])) {
			$model->attributes = $_POST['CutIntegralList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['integralView_op01']) && !empty($session['integralView_op01'])) {
				$criteria = $session['integralView_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('cutIntegral',array('model'=>$model,'index'=>$index));
	}


    public function actionEdit($index,$activity)
    {
        $model = new IntegralCutForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,'index'=>$activity,));
        }
    }

	public function actionApply()
	{
        if(CutForm::validateNowUser()){
            if (isset($_POST['CutForm'])) {
                $model = new CutForm("apply");
                $model->attributes = $_POST['CutForm'];
                if ($model->validate()) {
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('cutIntegral/view',array('index'=>$model->activity_id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->redirect(Yii::app()->createUrl('cutIntegral/view',array('index'=>$model->activity_id)));
                }
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
	}


    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_integral_cut where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'IntegralCutForm');
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
