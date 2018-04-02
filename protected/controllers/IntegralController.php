<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class IntegralController extends Controller
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
                'actions'=>array('new','edit','save','audit','delete','fileupload','fileRemove'),
                'expression'=>array('IntegralController','allowReadWrite'),
            ),
/*            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('IntegralController','allowReadOnly'),
            ),*/
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('IntegralController','allowAddReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('DE01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('DE01');
    }

    public static function allowAddReadOnly() {
        return Yii::app()->user->validFunction('DE02');
    }

    public function actionIndex($pageNum=0){
        if(IntegralForm::validateNowUser()){
            $model = new IntegralList;
            if (isset($_POST['IntegralList'])) {
                $model->attributes = $_POST['IntegralList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['integral_01']) && !empty($session['integral_01'])) {
                    $criteria = $session['integral_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }


    public function actionNew()
    {
        $model = new IntegralForm('new');
        if($model->validateNowUser(true)){
            $this->render('form',array('model'=>$model,));
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new IntegralForm('edit');
        if($model->validateNowUser()){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index)
    {
        $model = new IntegralForm('view');
        if($model->validateNowUser()){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'您的账号未绑定员工，请与管理员联系');
        }
    }


    public function actionSave()
    {
        if (isset($_POST['IntegralForm'])) {
            $model = new IntegralForm($_POST['IntegralForm']['scenario']);
            $model->attributes = $_POST['IntegralForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('integral/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    public function actionAudit()
    {
        if (isset($_POST['IntegralForm'])) {
            $model = new IntegralForm($_POST['IntegralForm']['scenario']);
            $model->attributes = $_POST['IntegralForm'];
            if ($model->validate()) {
                $model->state = 1;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('integral/edit',array('index'=>$model->id)));
            } else {
                $model->state = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new IntegralForm('delete');
        if (isset($_POST['IntegralForm'])) {
            $model->attributes = $_POST['IntegralForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "刪除失敗");
                $this->redirect(Yii::app()->createUrl('integral/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('integral/index'));
    }


    public function actionFileupload($doctype) {
        $model = new IntegralForm();
        if (isset($_POST['IntegralForm'])) {
            $model->attributes = $_POST['IntegralForm'];

            $id = ($_POST['IntegralForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new IntegralForm();
        if (isset($_POST['IntegralForm'])) {
            $model->attributes = $_POST['IntegralForm'];

            $docman = new DocMan($model->docType,$model->id,'IntegralForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from gr_integral where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'IntegralForm');
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