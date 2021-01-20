<?php
class Counter {
    public static function test(){
        $city =1;
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.apply_city='$city' and status_type in (14,5,13)")->queryScalar();

        $count = Yii::app()->db->createCommand()->select("count(*)")->from("hr_apply_support")
            ->where("status_type IN (6,2,4)")->queryScalar();
        return $count;
    }

//学分申请(被拒絕後提示)
    public static function getCreditApply() {
        $staffId = Yii::app()->user->staff_id();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_credit_request a")
            ->where("a.employee_id='$staffId' and a.state = 2")->queryScalar();
        return $count;
    }
//金银铜奖项申请(被拒絕後提示)
    public static function getPrizeApply() {
        $staffId = Yii::app()->user->staff_id();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_prize_request a")
            ->where("a.employee_id='$staffId' and a.state = 2")->queryScalar();
        return $count;
    }
//积分兑换列表(被拒絕後提示)
    public static function getGiftApply() {
        $staffId = Yii::app()->user->staff_id();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_gift_request a")
            ->where("a.employee_id='$staffId' and a.state = 2")->queryScalar();
        return $count;
    }
//学分专员确认(審核)
    public static function getCreditAuditOne() {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city IN ($city_allow) AND a.state = 1")->queryScalar();
        return $count;
    }
//学分审核(審核)
    public static function getCreditAuditTwo() {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_credit_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city IN ($city_allow) AND a.state = 4")->queryScalar();
        return $count;
    }
//积分兑换审核(審核)
    public static function getGiftAudit() {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_gift_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city IN ($city_allow) AND a.state = 1")->queryScalar();
        return $count;
    }
//奖项审核(審核)
    public static function getPrizeAudit() {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("gr_prize_request a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("b.city IN ($city_allow) AND a.state = 1")->queryScalar();
        return $count;
    }
}
?>