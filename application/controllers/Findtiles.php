<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Findtiles extends CI_Controller {



	public function index()
	{
        $this->load->view("index1.html");
	}
//	public function download(){
//		set_time_limit(0);
//        $t1 = microtime(true);
//		$zoomLevel = $this->input->post('level');
//		if(empty($zoomLevel)&&$zoomLevel==0){
//		    $zoomLevel=12;
//        }
//		$mapbarImgRoot = dirname(__FILE__)."../../../maptile/";
//		$mapbarImgRoot=str_replace("\\","/",$mapbarImgRoot);
//		//每层地图切片的文件夹名称
//		$levelFolder = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18');
//
//		//每层地图切片所跨的经度数
//		$cutImgLonRange=array(90,40,20,10,5,2,1,0.5,0.2,0.1,0.05,0.02,0.01,0.005,0.002,0.001,0.0005,0.0002,0.0001,0.00005);
//
//		//每层地图切片所跨的维度数
//		$cutImgLatRange=array(90*0.8,40*0.8,20*0.8,10*0.8,5*0.8,2*0.8,0.8,0.5*0.8,0.2*0.8,0.1*0.8,0.05*0.8,0.02*0.8,0.01*0.8,0.005*0.8,0.002*0.8,0.001*0.8,0.0005*0.8,0.0002*0.8,0.0001*0.8,0.00005*0.8);
//
//		$blockSize=array(10,10,10,10,10,10,10,10,10,10,50,50,50,50,50,50,50,50,50,50);
//
//		$clipXNum=ceil(360/$cutImgLonRange[$zoomLevel]);
//		$clipYNum=ceil(90/$cutImgLatRange[$zoomLevel]);
//
//		$clipLonRange = $cutImgLonRange[$zoomLevel];
//		$clipLatRange = $cutImgLatRange[$zoomLevel];
//
//		$clipXGap = -180-$clipLonRange/2;
//		//--------湖南范围的地图----------
//		$west = 112.432508;
//		$east = 112.908538;
//		$north =28.524827;
//		$south =28.200359;
//
//		$chinaLonRange = $east-$west;//湖南经度跨度
//		$chinaLatRange = $north-$south;//湖南维度跨度
//
//		$chinaClipXNum = ceil($chinaLonRange/$clipLonRange);
//		$chinaClipYNum = ceil($chinaLatRange/$clipLatRange);
//		$numberOfTiles=0;
//		$failOfTiles = 0;
//		$this->load->library('Covertor');
//		for($i=0;$i<$chinaClipXNum;$i++){
//            $lon = $west+$clipLonRange*$i;
//            $timeNow = time();
//            for($j=0;$j<$chinaClipYNum;$j++){
//                $lat = $south+$clipLatRange*$j;
//                $ll2mc = Covertor::convertLL2MC($lon,$lat);
//                $clipXNo = floor(floor($ll2mc[0]*pow(2,$zoomLevel-18))/256);
//                $clipYNo =floor(floor( $ll2mc[1]*pow(2,$zoomLevel-18))/256);
//
//                // echo "\n新的切片... => \n";
//                // echo '经纬度：'.$lon.' , '.$lat."\n";
//                // echo "-------------------------------\n";
//                // echo '切片序号：'.$clipXNo.' , '.$clipYNo."\n";
//                // echo "-------------------------------\n";
//                //切片分文件夹存放位置
//                $folderXNo =$clipYNo;
//                //切片分文件夹存放位置
//                $folderYNo =$clipYNo;
//                $fileXNo =$clipXNo;
//                $fileYNo =$clipYNo;
//                $imgPre = 'http://its.map.baidu.com:8002/traffic/TrafficTileService?';
//                // $imgDir = $levelFolder[$zoomLevel].'/'.$folderXNo.'_'.$folderYNo.'/';
//                // $imgName = $fileXNo.'_'.$fileYNo.'.png';
//                // $imgDir =  $zoomLevel.'/'.$folderXNo.'_'.$folderYNo.'/';
//                $imgDir =  $zoomLevel.'/'.$fileXNo.'/';
//                $imgName = $fileYNo.'.jpg';
//                $imgUrl = $imgPre."level=".$zoomLevel.'&x='.$clipXNo.'&y='.$clipYNo."&time=".$timeNow.'&label=web2D'."&v=081";
//
//                $localImgDir = $mapbarImgRoot.$imgDir;
//
//                $localImgName = $localImgDir.$imgName;
//
//                Covertor::createdir($localImgDir);
//                $return_content = Covertor::http_get_data($imgUrl);
//                $fp= @fopen($localImgName,"w"); //将文件绑定到流
//                fwrite($fp,$return_content); //写入文件
//                $numberOfTiles++;
//
//
//                if (!is_file($localImgName)){
//
//
//                        $data['longitude']=$lon;
//                        $data['latitude']=$lat;
//                        $data['zoomlevel']=$zoomLevel;
//                        $data['clipX']=$clipXNo;
//                        $data['clipY']=$clipYNo;
//                        $data['imgUrl']=$localImgName;
////                    	$color = Covertor::imagepgm($localImgName);
////                    	$data['color']=$color;
//                        $this->load->model('Titles_Model');
//                        $this->Titles_Model->addTitleinfo($data);
//                        $numberOfTiles++;
//                }
//            }
//		}
//		$t2 = microtime(true);
//		// echo '耗时'.round($t2-$t1,3).'秒';
//		//
//		// echo json_encode($msg);
//        $msg="共下载".$numberOfTiles."个切片,耗时".round($t2-$t1,3).'秒'.";失败:".$failOfTiles."个";
//        echo $msg;
//	}

    public function download(){
        set_time_limit(0);
        $t1 = microtime(true);
        $zoomLevel = $this->input->get('level');
        if(empty($zoomLevel)&&$zoomLevel==0){
            $zoomLevel=17;
        }
        if($zoomLevel>19){
            echo "<script> alert('级别不能超过19');history.back();</script>";
        }
        $mapbarImgRoot = dirname(__FILE__)."../../../maptile/";
        $mapbarImgRoot=str_replace("\\","/",$mapbarImgRoot);
        //每层地图切片的文件夹名称
        $levelFolder = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18');

        //每层地图切片所跨的经度数
        $cutImgLonRange=array(90,40,20,10,5,2,1,0.5,0.2,0.1,0.05,0.02,0.01,0.005,0.002,0.001,0.0005,0.0002,0.0001,0.00005);

        //每层地图切片所跨的维度数
        $cutImgLatRange=array(90*0.8,40*0.8,20*0.8,10*0.8,5*0.8,2*0.8,0.8,0.5*0.8,0.2*0.8,0.1*0.8,0.05*0.8,0.02*0.8,0.01*0.8,0.005*0.8,0.002*0.8,0.001*0.8,0.0005*0.8,0.0002*0.8,0.0001*0.8,0.00005*0.8);

        $blockSize=array(10,10,10,10,10,10,10,10,10,10,50,50,50,50,50,50,50,50,50,50);

        $clipXNum=ceil(360/$cutImgLonRange[$zoomLevel]);
        $clipYNum= (90/$cutImgLatRange[$zoomLevel]);

        $clipLonRange = $cutImgLonRange[$zoomLevel];
        $clipLatRange = $cutImgLatRange[$zoomLevel];

        $clipXGap = -180-$clipLonRange/2;
        //--------湖南范围的地图----------
        //------长张高速
//        29.4559490000,111.1320710000
//        28.1677610000,112.82692200
//        29.1439960000,110.56807900

//        $west = 110.56807900;
//        $east = 112.82692200;
//        $north =29.4559490000;
//        $south =28.1677610000;
        $west = $this->input->get('west');
        $east = $this->input->get('east');
        $north = $this->input->get('north');
        $south = $this->input->get('south');

//        $west = 110.738547;
//        $east = 113.756412;
//        $north =29.488997;
//        $south =27.948092;

        $chinaLonRange = $east-$west;//湖南经度跨度
        $chinaLatRange = $north-$south;//湖南维度跨度

        $chinaClipXNum = ceil($chinaLonRange/$clipLonRange);
        $chinaClipYNum = ceil($chinaLatRange/$clipLatRange);
        $numberOfTiles=0;
        $failOfTiles = 0;
        $this->load->library('Covertor');
        $precent=0;
        $ch=curl_init();
        for($i=0;$i<$chinaClipXNum;$i++){
            $lon = $west+$clipLonRange*$i;
            $timeNow = time();
            for($j=0;$j<$chinaClipYNum;$j++){
                $lat = $south+$clipLatRange*$j;
                $ll2mc = Covertor::convertLL2MC($lon,$lat);
                $piexl = $ll2mc[0]/pow(2,$zoomLevel-18);
                $piex2 = $ll2mc[1]/pow(2,$zoomLevel-18);
                $clipXNo = floor($ll2mc[0]*pow(2,$zoomLevel-18)/256);
                $clipYNo =floor($ll2mc[1]*pow(2,$zoomLevel-18)/256);
                $folderXNo =$clipYNo;
                //切片分文件夹存放位置
                $folderYNo =$clipYNo;
                $fileXNo =$clipXNo;
                $fileYNo =$clipYNo;
                $imgPre = 'http://its.map.baidu.com:8002/traffic/TrafficTileService?';
                $imgDir =  $zoomLevel.'/'.$fileXNo.'/';
                $imgName = $fileYNo.'.png';
                $imgUrl = $imgPre."level=".$zoomLevel.'&x='.$clipXNo.'&y='.$clipYNo."&time=".$timeNow."&v=081&smallflow=1&scaler=1&label=web2D";
                //http://its.map.baidu.com:8002/traffic/TrafficTileService?level=12&x=766&y=200&time=1486024988069&v=081&smallflow=1&scaler=1
                //'&label=web2D'.
                $localImgDir = $mapbarImgRoot.$imgDir;

                $localImgName = $localImgDir.$imgName;
                $data_imgUrl =base_url()."maptile/".$imgDir.$imgName;
                if (!is_file($localImgName)){
                    Covertor::createdir($localImgDir);

                    $timeout=5;
                    curl_setopt($ch,CURLOPT_URL,$imgUrl);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
                    $img=curl_exec($ch);

                    $size=strlen($img);
                    if($size!=0){
                        $data['longitude_right']=$lon;
                        $data['latitude_right']=$lat;
                        $data['zoomlevel']=$zoomLevel;
                        $data['clipX']=$clipXNo;
                        $data['clipY']=$clipYNo;
                        $data['imgUrl']=$data_imgUrl;
                        $this->load->model('Titles_Model');
                        $this->Titles_Model->addTitleinfo($data);
                        $numberOfTiles++;
                    }
                    $fp2=@fopen($localImgName,'a');
                    fwrite($fp2,$img);
                    fclose($fp2);
//                    sleep(5);
//                    ob_flush();
//                    if(copy($imgUrl,$localImgName)){
//                        $image = @imagecreatefrompng($localImgName);
//                        if($image){
//                            $data['longitude_right']=$lon;
//                            $data['latitude_right']=$lat;
//                            $data['zoomlevel']=$zoomLevel;
//                            $data['clipX']=$clipXNo;
//                            $data['clipY']=$clipYNo;
//                            $data['imgUrl']=$data_imgUrl;
//                            $this->load->model('Titles_Model');
//                            $this->Titles_Model->addTitleinfo($data);
//                        }
//                        $numberOfTiles++;
//
//                    }else{
//                        $failOfTiles++;
//                    }
                }
//                sleep(5);
//                flush();
                $precent = sprintf('%0.2f%%',($i*$j)/($chinaClipXNum*$chinaClipYNum)*100);
            }

            echo $precent."  ";
            if($i*$j == 20){
                echo "</br>";
            }
        }
        curl_close($ch);
        $t2 = microtime(true);

//        $msg="共下载".$numberOfTiles."个切片,耗时".round($t2-$t1,3).'秒'."失败:".$failOfTiles."个";
//        echo $msg;
    }



    public function checkcolor(){
		$lng = $this->input->post('lng');
		$lat = $this->input->post('lat');
		$this->load->library('Covertor');
		$ll2mc = Covertor::convertLL2MC($lng,$lat);
		$zoomLevel = 17;
		$clipXNo = floor(floor($ll2mc[0]*pow(2,$zoomLevel-18))/256);
		$clipYNo =floor(floor( $ll2mc[1]*pow(2,$zoomLevel-18))/256);
		$this->load->model('Titles_Model');
		$title = $this->Titles_Model->findTitle($clipXNo,$clipYNo);
		$color = Covertor::imagepgm($title->imgUrl);

	}

	public function getRouteByPiexl(){
        set_time_limit(0);
        $this->load->model('Titles_Model');
        $this->load->library('Covertor');
        $titles = $this->Titles_Model->findAllTiles();
        foreach ($titles as $title){
            $point=[];
            $image = @imagecreatefrompng($title->imgUrl);
            if($image){
                for($y = 0; $y < imagesy($image); $y++)
                {
                    for($x = 0; $x < imagesx($image); $x++)
                    {
                        $color = Covertor::getcolors($image,$x,$y);
                        if($color!="blank"){

                            array_push($point,[$x,$y]);

//                            array_push($point,array(
//                                "x"    => $x,
//                                "y"  => $y,
//                                "tid"  => $title->id,
//                            ));

                           // echo "瓦片:".$title->id." 像素x:".$x." 像素y:".$y." 颜色:".$color."</br>";
                        }

                    }
                }
//                foreach ($sortPoint as $points){
//                    echo "</br>";
//                    foreach($points as $point){
//                        printf('[%d,%d]', $point[0], $point[1]);
//                    }
//
//                }

               //$this->clustering($point);
                $this->sortpoint($point,2);
//                $result_clustering=array();
//                $tmp_point=array();
//                $x_cur=$point[0][0];
//                $y_cur = $point[0][1];
//                array_push($tmp_point,$point[0]);
//                array_splice($point,0,1);
//                for($i=0;$i<count($point);$i++){
//                    if(abs($x_cur-$point[$i][0])==1||abs($y_cur-$point[$i][1])==1){
//                        // echo "1";
//                        array_push($tmp_point,$point[$i]);
//                        // array_splice($point,$i,1);
//                    }else{
//                        // echo "0";
//                        array_push($result_clustering,$tmp_point);
//                        $tmp_point=array();
//                    }
//                    $x_cur = $point[$i][0];
//                    $y_cur = $point[$i][1];
//                }

                //echo var_dump($result_clustering);


               // $tmp_route = Covertor::clustering($point);
                break;
            }


            //echo var_dump($point);
        }

    }

    public function sortpoint($points,$dimention){
        $sorted_point=array();
        //$tmp_clustering=array();

        while(count($points)!=0){

            foreach ($points as $i=>$coordinates){

                $flag=0;
               // echo "1:sort的个数为:".count($sorted_point)."</br>";
                //for($k=count($sorted_point)-1;$k>=0;$k--){
                foreach ($sorted_point as $k=>$cluster){
                    $tmp=$sorted_point[$k];
//                    echo "2:当前point:[".$coordinates[0].",".$coordinates[1]."]</br>";
//                    echo "3:当前cluster";
//                    foreach ($sorted_point[$k] as $point){
//                        printf('[%d,%d],', $point[0], $point[1]);
//                     }
//                    echo "</br>";
                    foreach($sorted_point[$k] as $tmp_point){

                        $distance_tmp = $this->DistanceWith($coordinates,$tmp_point);
                        if($distance_tmp==1){
                            // $prePoint = $coordinates;
                            //  echo "point:[".$coordinates[0].",".$coordinates[1]."],";
                            array_push($tmp,$coordinates);
                            $sorted_point[$k] = $tmp;
                            unset($points[$i]);
//                            echo "4:在第".$k."cluster里插入[".$coordinates[0].",".$coordinates[1]."]</br>";
                            $flag = 1;
                            break;
                        }
                    }
                    if($flag==1){
                        break;
                    }
                }
                if($flag==0){
                    //array_push($tmp_clustering,$coordinates);
                   // echo "6:当前tmp:[".$tmp_clustering[0][0].",".$tmp_clustering[0][1]."]</br>";
                    unset($points[$i]);
                    array_push($sorted_point,array($coordinates));
//                    echo "6:当前point:[".$coordinates[0].",".$coordinates[1]."]</br>";
//                    echo "5:加入新的cluster</br></br></br>";

//                    foreach ($sorted_point as $z=>$pointss){
//                        echo "第".$z."个：";
//                        foreach ($pointss as $point)
//                            printf('[%d,%d],', $point[0], $point[1]);
//                        echo "</br>";
//                    }
//                    echo "</br></br></br>";

                }

//                foreach($tmp_clustering as $tmp_point){
//                    $distance_tmp = $this->DistanceWith($coordinates,$tmp_point);
//                    if($distance_tmp==1){
//                        // $prePoint = $coordinates;
//                      //  echo "point:[".$coordinates[0].",".$coordinates[1]."],";
//                        array_push($tmp_clustering,$coordinates);
//                        unset($points[$i]);
//                        break;
//                    }
//
//                }

            }

        }
       // echo
        $result_sort = $this->reSort($sorted_point);
        foreach ($result_sort as $z=>$pointss){
            echo "第".$z."个：";
            foreach ($pointss as $point)
                printf('[%d,%d],', $point[0], $point[1]);
            echo "</br>";
        }


    }

    public function reSort($sorted_point){
        $dayu_cluster = array();
        $xiaoyu_cluster = array();
        foreach ($sorted_point as $k=>$cluster){
            if(count($cluster)<10){

                array_push($xiaoyu_cluster,$cluster);
            }else{
                array_push($dayu_cluster,$cluster);
            }
        }
        echo var_dump(count($dayu_cluster));
        echo var_dump(count($xiaoyu_cluster));
        foreach ($xiaoyu_cluster as $cluster){
            foreach($cluster as $k=>$tmp_point){
                $is_in = $this->is_inCluster($tmp_point,$dayu_cluster);
                if($is_in[0]==1){
                    array_push($dayu_cluster[$is_in[1]],$tmp_point);
                }
            }
        }
        return $dayu_cluster;
    }

    public function is_inCluster($point,$clusters){
        $flag = 0;
        $result=[$flag];
        foreach($clusters as $i=>$cluster){
            foreach ($cluster as $tmp_point){
                $distance_tmp = $this->DistanceWith($point,$tmp_point);
                if($distance_tmp==1){
                    $flag = 1;
                    array_push($result,$i);
                    break;
                }
            }
            if($flag==1){
                break;
            }
        }
        return $result;
    }

    public function test(){
//        $prePoint=[166,174];
//        $nextPoint=[164,173];
//        echo $distance = $this->DistanceWith($prePoint,$nextPoint);
        $a=[[1,1],[2,2],[3,3]];
        $b=[4,4];
        $a[2]=$b;
        echo var_dump($a);
    }


    public function DistanceWith($prePoint,$nextPoint){
        $distance = 0;
        for ($n=0; $n<2; $n++) {
            $difference = abs($prePoint[$n] - $nextPoint[$n]);
            $distance  += $difference;
        }
        if($distance<3){
            return 1;
        }else{
            return 0;
        }
//        if($distance==0 || $distance==1 || $distance==2){
//            return 1;
//        }else{
//            return 0;
//        }
    }


    public function clustering($points){
        $this->load->library('KMeans/Kmeans');
        $clusters = Kmeans::solve($points);
// display the cluster centers and attached points
        // echo var_dump($clusters[0]->points)."</br>";
        foreach ($clusters as $i => $cluster){
            if(count($cluster)==0){
                continue;
            }
            printf("Cluster %s [%d,%d]: %d points\n", $i, $cluster[0], $cluster[1], count($cluster));
            foreach ($cluster as $point){
                printf('[%d,%d]', $point[0], $point[1]);
            }
            echo "</br>";
        }
    }




}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
