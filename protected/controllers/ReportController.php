<?php
class ReportController extends Controller
{
	protected static $actions = array(
						//'salessummary'=>'YB02',
						'creditslist'=>'YB02',
						'yearlist'=>'YB03',
						'cutlist'=>'YB04',
						'prizelist'=>'YB05',
						'stretchlist'=>'YB06',
					);
	
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		$act = array();
		foreach ($this->action as $key=>$value) { $act[] = $key; }
		return array(
			array('allow', 
				'actions'=>$act,
				'expression'=>array('ReportController','allowExecute'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSalessummary() {
		$model = new ReportY01Form;
		if (isset($_POST['ReportY01Form'])) {
			$model->attributes = $_POST['ReportY01Form'];
			if ($model->validate()) {
				$model->addQueueItem();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
			}
		}
		$this->render('form_y01',array('model'=>$model));
	}

    public function actionCreditslist() {
		$this->function_id = self::$actions['creditslist'];
		Yii::app()->session['active_func'] = $this->function_id;

        $model = new ReportY02Form;
        if (isset($_POST['ReportY02Form'])) {
            $model->attributes = $_POST['ReportY02Form'];
            if ($model->validate()) {
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y02',array('model'=>$model));
    }

    public function actionYearlist() {
		$this->function_id = self::$actions['yearlist'];
		Yii::app()->session['active_func'] = $this->function_id;

        $model = new ReportY03Form;
        if (isset($_POST['ReportY03Form'])) {
            $model->attributes = $_POST['ReportY03Form'];
            if ($model->validate()) {
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y03',array('model'=>$model));
    }

    public function actionCutlist() {
		$this->function_id = self::$actions['cutlist'];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new ReportY04Form;
        if (isset($_POST['ReportY04Form'])) {
            $model->attributes = $_POST['ReportY04Form'];
            if ($model->validate()) {
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y04',array('model'=>$model,'submit'=>Yii::app()->createUrl('report/cutlist')));
    }

    public function actionPrizelist() {
		$this->function_id = self::$actions['prizelist'];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new ReportY04Form;
        $model->id="RptPrizeList";
        $model->name=Yii::t("app","Prize List Report");
        if (isset($_POST['ReportY04Form'])) {
            $model->attributes = $_POST['ReportY04Form'];
            if ($model->validate()) {
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y04',array('model'=>$model,'submit'=>Yii::app()->createUrl('report/prizelist')));
    }

    public function actionStretchlist() {
		$this->function_id = self::$actions['stretchlist'];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new ReportY05Form;
        if (isset($_POST['ReportY05Form'])) {
            $model->attributes = $_POST['ReportY05Form'];
            if ($model->validate()) {
                $model->addQueueItem();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
        }
        $this->render('form_y05',array('model'=>$model,'submit'=>Yii::app()->createUrl('report/stretchlist')));
    }

	public static function allowExecute() {
		return Yii::app()->user->validFunction(self::$actions[Yii::app()->controller->action->id]);
	}
}
?>
