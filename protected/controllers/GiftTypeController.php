<?php

class GiftTypeController extends Controller
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
                'actions'=>array('new','edit','delete','save','fileupload','fileRemove'),
                'expression'=>array('GiftTypeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('GiftTypeController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('view','fileDownload'),
                'expression'=>array('GiftTypeController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS04');
    }

    public static function allowRead() {
        return Yii::app()->user->validFunction('SS04')||Yii::app()->user->validFunction('EX02')||Yii::app()->user->validFunction('GA02')||Yii::app()->user->validFunction('SR03');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new GiftTypeList;
		if (isset($_POST['GiftTypeList'])) {
			$model->attributes = $_POST['GiftTypeList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['giftType_op01']) && !empty($session['giftType_op01'])) {
				$criteria = $session['giftType_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['GiftTypeForm'])) {
			$model = new GiftTypeForm($_POST['GiftTypeForm']['scenario']);
			$model->attributes = $_POST['GiftTypeForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('giftType/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new GiftTypeForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new GiftTypeForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new GiftTypeForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new GiftTypeForm('delete');
        if (isset($_POST['GiftTypeForm'])) {
            $model->attributes = $_POST['GiftTypeForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('giftType/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("dialog","This record is already in use"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('giftType/index'));
        }
    }


    public function actionFileupload($doctype) {
        $model = new GiftTypeForm();
        if (isset($_POST['GiftTypeForm'])) {
            $model->attributes = $_POST['GiftTypeForm'];

            $id = ($_POST['GiftTypeForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new GiftTypeForm();
        if (isset($_POST['GiftTypeForm'])) {
            $model->attributes = $_POST['GiftTypeForm'];

            $docman = new DocMan($model->docType,$model->id,'GiftTypeForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
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
