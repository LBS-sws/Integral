<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class PrizeSearchForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
	public $prize_type;
	public $prize_point;
	public $apply_date;
	public $remark;
	public $reject_note;
	public $state = 0;
	public $city;
	public $lcu;
	public $luu;
	public $lcd;
	public $lud;


    public $no_of_attm = array(
        'rpri'=>0
    );
    public $docType = 'RPRI';
    public $docMasterId = array(
        'rpri'=>0
    );
    public $files;
    public $removeFileId = array(
        'rpri'=>0
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('integral','Record ID'),
            'employee_id'=>Yii::t('integral','Employee Name'),
            'employee_name'=>Yii::t('integral','Employee Name'),
            'prize_type'=>Yii::t('integral','Prize Name'),
            'prize_point'=>Yii::t('integral','Prize Point'),
			'remark'=>Yii::t('integral','Remark'),
            'reject_note'=>Yii::t('integral','Reject Note'),
            'city'=>Yii::t('integral','City'),
            'apply_date'=>Yii::t('integral','apply for time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id, employee_name, prize_type, prize_point, apply_date, remark, reject_note, lcu, luu, lcd, lud','safe'),

            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}
	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,docman$suffix.countdoc('RPRI',a.id) as rpridoc")
            ->from("gr_prize_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
                $this->prize_type = $row['prize_type'];
                $this->prize_point = $row['prize_point'];
                $this->apply_date = $row['apply_date'];
                $this->remark = $row['remark'];
                $this->reject_note = $row['reject_note'];
                $this->state = $row['state'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
                $this->city = $row['city'];
                $this->apply_date = CGeneral::toDate($row['apply_date']);
                $this->no_of_attm['rpri'] = $row['rpridoc'];
				break;
			}
		}
		return true;
	}

	public function creditClone(){
	    $html="<table class='table table-striped table-bordered table-hover'><thead><tr>";
	    $html.="<th width='1%'>&nbsp;</th>";
	    $html.="<th>".Yii::t("app","Credit").Yii::t("integral","apply for time")."</th>";
	    $html.="<th>".Yii::t("integral","Integral Code")."</th>";
	    $html.="<th>".Yii::t("integral","Integral Name")."</th>";
        $html.="<th>".Yii::t("integral","Integral Num")."</th>";
	    $html.="<th>".Yii::t("integral","Cut gift")."</th>";
	    $html.="</tr></thead>";
        $rows = Yii::app()->db->createCommand()
            ->select("a.credit_req_id,a.prize_json,a.rec_date,a.credit_point,b.credit_code,b.credit_name,b.category")
            ->from("gr_credit_point a")
            ->leftJoin("gr_credit_type b","a.credit_type = b.id")
            ->where("FIND_IN_SET($this->id,prize_id_list) ")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $link = Yii::app()->createUrl('creditSearch/view',array("index"=>$row["credit_req_id"]));
                $list = $this->getPrizeJson($row["prize_json"]);
                $html.="<tr>";
                $html.="<td><a target='_blank' href='$link'><span class='glyphicon glyphicon-eye-open'></span></a></td>";
                $html.="<td>".$row["rec_date"]."</td>";
                $html.="<td>".$row["credit_code"]."</td>";
                $html.="<td>".$row["credit_name"]."</td>";
                $html.="<td>".$row["credit_point"]."</td>";
                $html.="<td>".$list[$this->id]."</td>";
                $html.="</tr>";
            }
        }
        $html.="</table>";
	    return $html;
    }

    private function getPrizeJson($str){
        $list = explode("+",$str);
        $arr = array();
        foreach ($list as $item){
            if(!empty($item)){
                $item = json_decode($item,true);
                $arr[$item['id']] = $item['num'];
            }
        }
        if(!key_exists($this->id,$arr)){
            $arr[$this->id] = "error";
        }
        return $arr;
    }
}
