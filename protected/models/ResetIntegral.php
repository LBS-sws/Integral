<?php
class ResetIntegral{

    public function start(){
        set_time_limit(0);
        Yii::app()->db->createCommand()->update("gr_credit_point",array(
            'prize_id_list'=>'',
            'prize_json'=>''
        ),"id>0");
        echo "start:<br/>";
        echo "-----------------------<br/>";
        $this->prizeList();
        echo "-----------------------<br/>";
        echo "success end";
    }

    private function prizeList(){
        $prizeList = Yii::app()->db->createCommand()
            ->select("id,employee_id,prize_point,apply_date")
            ->from("gr_prize_request")->where("state = 3")
            ->order("employee_id asc,apply_date asc")->queryAll();

        $staff_id = 0;//員工id
        $creditList = false;//員工學分列表
        $creditKey = 0;
        if($prizeList){
            foreach ($prizeList as $prize){
                //echo "gr_prize_request:".$prize["id"]."<br/>";
                $prizeYear = date("Y",strtotime($prize["apply_date"]));
                $sum = intval($prize["prize_point"]);
                $startNum = 0;
                if($staff_id!=$prize["employee_id"]){ //查詢員工的學分
                    $staff_id = $prize["employee_id"];
                    echo "staff_id:".$staff_id."<br/>";
                    $creditKey = 0;
                    $creditList = Yii::app()->db->createCommand()
                        ->select("id,rec_date,expiry_date,credit_point")->from("gr_credit_point")
                        ->where("employee_id='$staff_id'")->order("rec_date asc")->queryAll();
                    //$creditList = $creditList?$creditList:array();
                }

                if ($creditList){ //開始記錄扣減學分
                    for($i=$creditKey;$i<count($creditList);$i++){
/*                        $minYear = date("Y",strtotime($creditList[$i]["rec_date"]));
                        $maxYear = date("Y",strtotime($creditList[$i]["expiry_date"]));
                        if($prizeYear>$maxYear||$prizeYear<$minYear){
                            echo "error:Year error for gr_prize_request(".$prize["id"].")<br/>";
                            break;
                        }*/
                        $nowNum = intval($creditList[$i]["credit_point"]);
                        $updateNum = $sum<($startNum+$nowNum)?($sum - $startNum):$nowNum;
                        //echo $prize["id"].":".$sum."_".$startNum."_".$nowNum."_".$updateNum."<br/>";
                        $startNum+=$nowNum;
                        if($nowNum > 0){//記錄
                            $sql = "update gr_credit_point set
                            prize_id_list = CONCAT(prize_id_list,',".$prize["id"]."'),
                            prize_json = CONCAT(prize_json,'+".json_encode(array('id'=>$prize["id"],'num'=>$updateNum))."')
                             WHERE id=".$creditList[$i]["id"];
                            Yii::app()->db->createCommand($sql)->execute();
                        }
                        if($sum<=$startNum){ //不需要繼續扣分
                            $creditList[$i]["credit_point"] = $startNum - $sum;
                            $creditKey = $i;
                            break;
                        }
                    }
                }

                if($sum > $startNum){
                    echo "error:Score on min for gr_prize_request(".$prize["id"].")-(sum:$sum>startNum:$startNum)<br/>";
                }
            }
        }
    }

}
