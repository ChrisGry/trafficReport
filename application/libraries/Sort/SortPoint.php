<?php
class SortPoint{
    protected $prePoint;

    public function sortpoint($points,$dimention){
        $sorted_point=[];
        while(count($points)){
            $prePoint = $points[0];
            $tmp_clustering=[$prePoint];
            foreach ($points as $i=>$coordinates){
                if($i==0){
                    continue;
                }
                $distance = $this->DistanceWith($prePoint,$coordinates,$dimention);
                if($distance ==1){
                    array_push($tmp_clustering,$coordinates);
                    array_splice($points,$i,1);
                }else{
                    array_push($sorted_point,$tmp_clustering);
                    break;
                }
            }
        }
        echo var_dump($sorted_point);
    }


    public function DistanceWith($prePoint,$nexrPoint,$dimention){
        $distance = 0;
        for ($n=0; $n<$dimention; $n++) {
            $difference = $prePoint[$n] - $nexrPoint[$n];
            $distance  += abs($difference);
        }
        return $distance;
    }

}
?>
