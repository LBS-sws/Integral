<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 */
class IntegralSearchController extends Controller
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
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('IntegralSearchController','allowReadWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SR01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR02');
    }

    public static function allowAddReadOnly() {
        return Yii::app()->user->validFunction('SR03');
    }

    public function actionIndex($pageNum=0){
        $model = new IntegralSearchList;
        if (isset($_POST['IntegralSearchList'])) {
            $model->attributes = $_POST['IntegralSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['integralSearch_01']) && !empty($session['integralSearch_01'])) {
                $criteria = $session['integralSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index)
    {
        $model = new IntegralSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
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