<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class DrawRoute extends CI_Controller {
    private static $point_cluster=array();
    private static $points =array();
    public function __construct(){
        //$this->need_login = true;//控制是否需要登录
        parent::__construct();
        $this->load->model('Route_Model');
        $this->load->model('Points_Model');
        $this->load->model("User_Model");
        $this->load->model("Titles_Model");
    }
    public function need_login(){
        $user=$this->session->userdata('user');
       // echo $user;
        if($user == ""){
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=userlogin/login\" >";
        }else{
            return  true;
        }
    }

    public function index(){
        $this->need_login();
        $roadId = $this->input->get('id');
        $direction = $this->input->get('dire')==null?0:(int)$this->input->get('dire');
        $data['username']=$this->session->userdata('user');
        $data['road'] = $this->Route_Model->findRouteByRoadId($roadId);
        $data['road_tile'] = $this->Route_Model->findRoadTileByRoadId($roadId,$direction);

        $data['roads'] = $this->Route_Model->findAllRoute();
        $cluster = $this->Points_Model->findPointsByRoadId($roadId);
        $tiles = @json_decode($data['road_tile']->tile_sort);

        if(count($data['road_tile']) ==0){
            if($direction==1){
                $data['init_center'] = $data['road']->start;
            }else{
                $data['init_center'] = $data['road']->end;
            }

        }else{

            $tileid= end($tiles);

            $tile= $this->Titles_Model->findTitleById($tileid->id);
            $lnglat = [$tile->longitude_right,$tile->latitude_right];
            $data['init_center'] =implode(",",$lnglat);
        }
        $data['direticon']=$direction;
        $data['tiles'] = $tiles==null?[]:$tiles;
        $data['roadSeq']=$this->Route_Model->findRouteSeqByRoadId($roadId);
        $save_data['locking'] = 1;
        $this->Route_Model->updateRoad($roadId,$save_data);
        $this->load->view("road_map.html",$data);

    }

    public function findtile(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $tile_x = $this->input->get('tile_x');
        $tile_y = $this->input->get('tile_y');
        $this->load->model('Titles_Model');
        $this->load->model('Route_Model');
        $data['tile'] = $this->Titles_Model->findTitle($tile_x,$tile_y);
        $data['routes'] = $this->Route_Model->findAllRoute();
        $data['clusters'] = $this->getRouteByPiexl($data['tile']);
        $this->load->view("draw_route.html",$data);
    }

    public function findpoint(){
        $tile_x = $this->input->get('x');
        $tile_y = $this->input->get('y');
        $dire = $this->input->get('d');
        $tile = $this->Titles_Model->findTitle($tile_x,$tile_y);
        $points = $this->Points_Model->findPointsByTileId($tile->id,$dire);

        $result=[];
        if($points !=null){
            $arrpoint = json_decode($points->points);
            foreach ($arrpoint as $point){
                $tmp = explode(',',$point->id);
                array_push($result,$tmp);
            }
        }
        echo json_encode(["id"=>$tile->id,"point"=>$result]);

    }

    public function roadlist(){
        $this->need_login();
        $page = $this->input->get('page');
        if($page == null || $page == "")
        {
            $page = 0;
        }
        //每页数量
        $number = 10;
        $data['username']=$this->session->userdata('user');
        $userid=$this->session->userdata('userid');
        $data['user'] = $this->User_Model->getAdminById($userid);
        if($data['user']->last_road==null || $data['user']->last_road==" "){
            $data['last_route'] = $this->Route_Model->findFistUncheckedRoute();
        }else{
            $data['last_route'] = $this->Route_Model->findRouteByRoadId($data['user']->last_road);
        }
        $data['routes'] = $this->Route_Model->findAllCheckedRoadLimit($number,$page*$number);
        $amoutn = $this->Route_Model->getCheckedRoadAmount();
        $data['page'] = $page;
        $data['pageNo']  = $amoutn/$number;
        $data['pageNav'] = $this->getPageNav($data['pageNo'],$page);
        $data['amoutn'] = $amoutn;
        $this->load->view("road.html",$data);
    }



    public function nexttile(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $tileId = $this->input->get("tileid");
        $this->load->model('Titles_Model');
        $this->Titles_Model->saveTileStatus($tileId);
        $data['tile'] = $this->Titles_Model->findFistUncheckedTitle();
        $data['clusters'] = $this->getRouteByPiexl($data['tile']);
        $this->load->view("draw_route.html",$data);
    }

    public function savePoints(){
        $points = $this->input->post('point[]');

        $data['roadId'] = $this->input->post('roadId');
        $data['direction'] = $this->input->post('directions');
        $x = $this->input->post('tile_x');
        $y = $this->input->post('tile_y');
        $img = $this->input->post('img');
        $tileInfo =$this->Titles_Model->findTitle($x,$y);
        $data['tileId'] = $tileInfo->id;
        $data['points'] = $this->input->post('points');

        $acrossPoint = $this->input->post('jiaochas');
        $jiaochaRoad = $this->input->post('jiaocha_road');

        $road = $this->Route_Model->findRouteByRoadId($data['roadId']);

        if($acrossPoint!="[]"){
            $jiaochaarray = [$jiaochaRoad=>$acrossPoint];
            $jiaocha['acrossPoint'] = json_encode($jiaochaarray);
            $jiaocha['acrossPoint'] = $road->acrossPoint.",".$jiaocha['acrossPoint'] ;
            $save_accross = $this->Route_Model->updateRoad($data['roadId'],$jiaocha);
        }
        $roadtile = $this->Route_Model->findRoadTileByRoadId($data['roadId'],$data['direction']);
        if(count($roadtile)==0){
            $tiles = [];
            array_push($tiles,["id"=>$data['tileId']]);
            $roadtile_data['tile_sort'] = json_encode($tiles);
            $roadtile_data['roadId'] = $data['roadId'];
            $roadtile_data['direction'] = $data['direction'];
            $save_road_tile = $this->Route_Model->saveRoadTile($roadtile_data);
        }else{
            $tiles = json_decode($roadtile->tile_sort)==null?[]:json_decode($roadtile->tile_sort);
            $isInarray = false;
            foreach ($tiles as $tile){
                if($tile->id == $data['tileId']){
                    $isInarray =true;
                }
            }
            if(!$isInarray){
                array_push($tiles,["id"=>$data['tileId']]);
            }
            $roadtile_data['tile_sort'] = json_encode($tiles);
            $save_road_tile = $this->Route_Model->updateRoadTile($roadtile->id,$roadtile_data);
        }


        $hasResult = $this->Points_Model->hasResult($data['roadId'],$data['tileId'],$data['direction']);
        if($hasResult == null){
            $save_id = $this->Points_Model->savePointsinfo($data);
        }else{
            $dataUpDate['points'] = $data['points'];
            $save_id = $this->Points_Model->updatePointsinfo($data['roadId'],$data['tileId'],$data['direction'],$dataUpDate);
        }
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $database64 = base64_decode($img);
        $file = dirname(__FILE__)."../../../maptile/17/".$x."/".$y.".png";
        $success = file_put_contents($file, $database64);
        if($save_id!=null){
            echo "保存成功";
        }else{
            echo "保存失败";
        }

    }

    public function saveRoad(){
        $roadId=$this->input->post("roadId");
        $tiles=$this->input->post("tiles");
        $data['title_sort'] =$tiles;
        $data["checked"]=1;
        $data["locking"]=0;
        $save_id = $this->Route_Model->checkedRoad($roadId,$data);
        if($save_id!=null){
            echo "保存成功";
        }else{
            echo "保存失败";
        }
    }

    public function getPageNav($pageNo,$page)
    {


        $i= $page-3<0?0:$page-3;
        $j = $page+3>$pageNo?$pageNo:$page+3;
        //echo "数量：".$amoutn."页数：".$pageNo."i".$i."j:".$j;
        $pageNav = "";
        for($i;$i<$j;$i++)
        {
            $pageNav =$pageNav."<li class='paginate_button'><a href='".site_url('drawroute/roadlist?page=').$i."'>".($i+1)."</a></li>";
        }

        return $pageNav;
    }
    public function adminRL(){
        $this->need_login();
    }

    public function saveJXPoints(){
        $points = $this->input->post('allPoint');
        $routeNames = $this->input->post('allRoute');
        $tileId =  $this->input->post('tileId');
        $array_route=explode(",",$routeNames);
        $this->load->model('Route_Model');
        foreach ($array_route as $route){
            if($route==""){
                continue;
            }
            $hasRoute = $this->Route_Model->getRouteByName($route);
            if($hasRoute==null){
                $data_route['routeName'] = $route;
                $data_route['acrossPoint'] =$points;
                $data_route['tileId']=$tileId;
                $this->Route_Model->saveRouteinfo($data_route);
            }else{
                $data['acrossPoint'] =$points.$hasRoute->acrossPoint;
                $this->Route_Model->saveRouteinfoByID($hasRoute->id,$data);
            }
        }
        echo 1;
    }

    public function getRouteByPiexl($title){
        set_time_limit(0);
        $this->load->model('Titles_Model');
        $this->load->library('Covertor');
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
                       // echo "[".$x.",".$y."],";
                    }
                }
            }
            DrawRoute::$points = $point;
            $this->load->library('KMeans/Kmeans');
            //$clusters = Kmeans::solve($point);
           $clusters = $this->sort2();
//            $clusters = $this->sortPoint($point);
           return $clusters;
//            echo var_dump($clusters);

//                foreach($point as $pointss){
//                    printf('[%d,%d],', $pointss[0], $pointss[1]);
//                }
//            return null;
        }else{
            return null;
        }

    }

    public function sort2(){
        //初始化
        $sorted_point=array();
        $tmp_clustering=array();

        //遍历,每循环一次出一个点群
        while(count(DrawRoute::$points)!=0){
            //设置第一个点
            $firstPoint = DrawRoute::$points[0];
            array_splice(DrawRoute::$points,0,1);
            array_push(DrawRoute::$point_cluster,$firstPoint);
            //开始往下遍历
            $this->getAdjoinPoints(array($firstPoint));

            array_push($tmp_clustering,DrawRoute::$point_cluster);
            DrawRoute::$point_cluster=array();

        }
//        foreach($tmp_clustering as $pointsss){
//            foreach($pointsss as $pointss){
//                printf('[%d,%d],', $pointss[0], $pointss[1]);
//            }
//            echo "</br></br></br>";
//        }

        //echo var_dump($tmp_clustering);
        return $tmp_clustering;
    }


    public function getAdjoinPoints($temppoint){
        $result = null;
        $tmpCluster=array();
        if(count($temppoint)!=0){
            foreach($temppoint as $point){
                foreach (DrawRoute::$points as $i=>$coordinates) {
                    if($this->DistanceWith($point,$coordinates)){
                        array_push($tmpCluster,$coordinates);
                        array_push(DrawRoute::$point_cluster,$coordinates);
                        array_splice(DrawRoute::$points,$i,1);
                    }
                }
            }

//            echo "</br>";
            $this->getAdjoinPoints($tmpCluster);
        }else{

            return;
        }

    }



    public function sortPoint($points){
        $sorted_point=array();
        $tmp_clustering=array();

        while(count($points)!=0){
            foreach ($points as $i=>$coordinates){

                $flag=0;
                for($k=count($sorted_point)-1;$k>=0;$k--){
                    $tmp=$sorted_point[$k];

                    foreach($sorted_point[$k] as $tmp_point){

                        $distance_tmp = $this->DistanceWith($coordinates,$tmp_point);
                        if($distance_tmp==1){
                            array_push($tmp,$coordinates);
                            $sorted_point[$k] = $tmp;
                            unset($points[$i]);
                            $flag = 1;
                            break;
                        }
                    }
                    if($flag==1){
                        break;
                    }
                }
                if($flag==0){
                    unset($points[$i]);
                    array_push($sorted_point,array($coordinates));
                }

            }
        }
        return $this->reSort($sorted_point);

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

    public function sd($firstPoint,$points){
        $sort_array = array();
        $adjoin_point=$this->checkpoint($firstPoint,$points);
        if($adjoin_point!=null){
            $data['sort_array'] = $adjoin_point['point'];
        }


        $data['sort_array'] = $sort_array;
        $data['points'] = $points;
        return $data;
    }

    public function savePoint($sort_array,$point_cluster){
        foreach ($point_cluster as $point){
            array_push($sort_array,$point);
        }
    }

    public function test(){
       $points=[[73,0],[74,0],[75,0],[78,0],[79,0],[80,0],[72,1],[73,1],[74,1],[78,1],[79,1],[71,2],[72,2],[73,2],[77,2],[78,2],[70,3],[71,3],[72,3],[76,3],[77,3],[78,3],[69,4],[70,4],[71,4],[75,4],[76,4],[68,5],[69,5],[70,5],[74,5],[75,5],[67,6],[68,6],[69,6],[73,6],[74,6],[75,6],[66,7],[67,7],[68,7],[72,7],[73,7],[74,7],[66,8],[67,8],[71,8],[72,8],[73,8],[65,9],[66,9],[70,9],[71,9],[72,9],[64,10],[65,10],[69,10],[70,10],[71,10],[63,11],[64,11],[68,11],[69,11],[70,11],[62,12],[63,12],[64,12],[67,12],[68,12],[69,12],[61,13],[62,13],[63,13],[66,13],[67,13],[68,13],[60,14],[61,14],[62,14],[65,14],[66,14],[67,14],[59,15],[60,15],[61,15],[65,15],[66,15],[59,16],[60,16],[64,16],[65,16],[58,17],[59,17],[63,17],[64,17],[57,18],[58,18],[59,18],[62,18],[63,18],[64,18],[57,19],[58,19],[61,19],[62,19],[63,19],[56,20],[57,20],[61,20],[62,20],[55,21],[56,21],[57,21],[60,21],[61,21],[62,21],[55,22],[56,22],[59,22],[60,22],[61,22],[54,23],[55,23],[56,23],[59,23],[60,23],[54,24],[55,24],[58,24],[59,24],[60,24],[53,25],[54,25],[55,25],[58,25],[59,25],[53,26],[54,26],[57,26],[58,26],[52,27],[53,27],[54,27],[57,27],[58,27],[52,28],[53,28],[56,28],[57,28],[52,29],[53,29],[56,29],[57,29],[51,30],[52,30],[55,30],[56,30],[57,30],[51,31],[52,31],[55,31],[56,31],[51,32],[52,32],[55,32],[56,32],[50,33],[51,33],[54,33],[55,33],[56,33],[50,34],[51,34],[54,34],[55,34],[49,35],[50,35],[51,35],[54,35],[55,35],[49,36],[50,36],[51,36],[53,36],[54,36],[55,36],[49,37],[50,37],[53,37],[54,37],[49,38],[50,38],[53,38],[54,38],[48,39],[49,39],[50,39],[53,39],[54,39],[48,40],[49,40],[52,40],[53,40],[54,40],[48,41],[49,41],[52,41],[53,41],[48,42],[49,42],[52,42],[53,42],[47,43],[48,43],[51,43],[52,43],[53,43],[47,44],[48,44],[51,44],[52,44],[47,45],[48,45],[51,45],[52,45],[46,46],[47,46],[51,46],[52,46],[46,47],[47,47],[50,47],[51,47],[45,48],[46,48],[47,48],[50,48],[51,48],[45,49],[46,49],[49,49],[50,49],[51,49],[45,50],[46,50],[49,50],[50,50],[44,51],[45,51],[49,51],[50,51],[43,52],[44,52],[45,52],[48,52],[49,52],[43,53],[44,53],[48,53],[49,53],[42,54],[43,54],[44,54],[47,54],[48,54],[42,55],[43,55],[46,55],[47,55],[48,55],[41,56],[42,56],[46,56],[47,56],[40,57],[41,57],[42,57],[45,57],[46,57],[47,57],[40,58],[41,58],[44,58],[45,58],[46,58],[39,59],[40,59],[44,59],[45,59],[38,60],[39,60],[40,60],[43,60],[44,60],[37,61],[38,61],[39,61],[42,61],[43,61],[44,61],[36,62],[37,62],[38,62],[41,62],[42,62],[43,62],[35,63],[36,63],[37,63],[41,63],[42,63],[35,64],[36,64],[40,64],[41,64],[34,65],[35,65],[39,65],[40,65],[41,65],[33,66],[34,66],[35,66],[38,66],[39,66],[40,66],[32,67],[33,67],[34,67],[37,67],[38,67],[39,67],[31,68],[32,68],[33,68],[36,68],[37,68],[38,68],[30,69],[31,69],[32,69],[36,69],[37,69],[29,70],[30,70],[31,70],[35,70],[36,70],[29,71],[30,71],[34,71],[35,71],[28,72],[29,72],[33,72],[34,72],[35,72],[27,73],[28,73],[32,73],[33,73],[34,73],[26,74],[27,74],[28,74],[31,74],[32,74],[33,74],[25,75],[26,75],[27,75],[30,75],[31,75],[32,75],[24,76],[25,76],[26,76],[30,76],[31,76],[23,77],[24,77],[25,77],[29,77],[30,77],[23,78],[24,78],[28,78],[29,78],[22,79],[23,79],[27,79],[28,79],[29,79],[21,80],[22,80],[23,80],[26,80],[27,80],[28,80],[20,81],[21,81],[22,81],[25,81],[26,81],[27,81],[20,82],[21,82],[24,82],[25,82],[26,82],[19,83],[20,83],[24,83],[25,83],[18,84],[19,84],[20,84],[23,84],[24,84],[25,84],[18,85],[19,85],[22,85],[23,85],[24,85],[17,86],[18,86],[22,86],[23,86],[17,87],[18,87],[21,87],[22,87],[16,88],[17,88],[21,88],[22,88],[15,89],[16,89],[17,89],[20,89],[21,89],[15,90],[16,90],[20,90],[21,90],[15,91],[16,91],[19,91],[20,91],[14,92],[15,92],[19,92],[20,92],[14,93],[15,93],[18,93],[19,93],[14,94],[15,94],[18,94],[19,94],[13,95],[14,95],[17,95],[18,95],[19,95],[13,96],[14,96],[17,96],[18,96],[13,97],[14,97],[17,97],[18,97],[12,98],[13,98],[14,98],[17,98],[18,98],[12,99],[13,99],[16,99],[17,99],[12,100],[13,100],[16,100],[17,100],[12,101],[13,101],[16,101],[17,101],[12,102],[13,102],[16,102],[17,102],[12,103],[13,103],[15,103],[16,103],[17,103],[11,104],[12,104],[13,104],[15,104],[16,104],[17,104],[11,105],[12,105],[13,105],[15,105],[16,105],[17,105],[11,106],[12,106],[13,106],[15,106],[16,106],[17,106],[11,107],[12,107],[13,107],[15,107],[16,107],[17,107],[11,108],[12,108],[13,108],[15,108],[16,108],[17,108],[11,109],[12,109],[13,109],[15,109],[16,109],[17,109],[11,110],[12,110],[13,110],[15,110],[16,110],[17,110],[12,111],[13,111],[16,111],[17,111],[12,112],[13,112],[16,112],[17,112],[12,113],[13,113],[16,113],[17,113],[12,114],[13,114],[16,114],[17,114],[12,115],[13,115],[16,115],[17,115],[12,116],[13,116],[14,116],[16,116],[17,116],[18,116],[13,117],[14,117],[17,117],[18,117],[13,118],[14,118],[17,118],[18,118],[13,119],[14,119],[17,119],[18,119],[13,120],[14,120],[17,120],[18,120],[19,120],[13,121],[14,121],[15,121],[18,121],[19,121],[14,122],[15,122],[18,122],[19,122],[14,123],[15,123],[18,123],[19,123],[14,124],[15,124],[18,124],[19,124],[14,125],[15,125],[18,125],[19,125],[14,126],[15,126],[18,126],[19,126],[20,126],[14,127],[15,127],[16,127],[19,127],[20,127],[15,128],[16,128],[19,128],[20,128],[15,129],[16,129],[19,129],[20,129],[15,130],[16,130],[19,130],[20,130],[15,131],[16,131],[19,131],[20,131],[15,132],[16,132],[19,132],[20,132],[15,133],[16,133],[19,133],[20,133],[21,133],[15,134],[16,134],[20,134],[21,134],[15,135],[16,135],[17,135],[20,135],[21,135],[15,136],[16,136],[17,136],[20,136],[21,136],[16,137],[17,137],[20,137],[21,137],[16,138],[17,138],[20,138],[21,138],[16,139],[17,139],[20,139],[21,139],[16,140],[17,140],[20,140],[21,140],[16,141],[17,141],[20,141],[21,141],[253,141],[254,141],[255,141],[16,142],[17,142],[20,142],[21,142],[251,142],[252,142],[253,142],[254,142],[255,142],[16,143],[17,143],[20,143],[21,143],[248,143],[249,143],[250,143],[251,143],[252,143],[253,143],[16,144],[17,144],[20,144],[21,144],[247,144],[248,144],[249,144],[250,144],[16,145],[17,145],[20,145],[21,145],[244,145],[245,145],[246,145],[247,145],[248,145],[254,145],[255,145],[16,146],[17,146],[20,146],[21,146],[242,146],[243,146],[244,146],[245,146],[246,146],[252,146],[253,146],[254,146],[255,146],[16,147],[17,147],[20,147],[21,147],[240,147],[241,147],[242,147],[243,147],[244,147],[249,147],[250,147],[251,147],[252,147],[253,147],[254,147],[16,148],[17,148],[20,148],[21,148],[239,148],[240,148],[241,148],[242,148],[248,148],[249,148],[250,148],[251,148],[252,148],[16,149],[17,149],[20,149],[21,149],[237,149],[238,149],[239,149],[240,149],[246,149],[247,149],[248,149],[249,149],[16,150],[17,150],[20,150],[21,150],[236,150],[237,150],[238,150],[244,150],[245,150],[246,150],[247,150],[16,151],[17,151],[20,151],[21,151],[234,151],[235,151],[236,151],[242,151],[243,151],[244,151],[245,151],[16,152],[17,152],[20,152],[21,152],[233,152],[234,152],[235,152],[240,152],[241,152],[242,152],[243,152],[16,153],[17,153],[20,153],[21,153],[232,153],[233,153],[234,153],[238,153],[239,153],[240,153],[241,153],[16,154],[17,154],[20,154],[21,154],[231,154],[232,154],[237,154],[238,154],[239,154],[16,155],[17,155],[20,155],[21,155],[230,155],[231,155],[236,155],[237,155],[238,155],[16,156],[17,156],[20,156],[21,156],[229,156],[230,156],[234,156],[235,156],[236,156],[237,156],[16,157],[17,157],[20,157],[21,157],[228,157],[229,157],[233,157],[234,157],[235,157],[16,158],[17,158],[20,158],[21,158],[227,158],[228,158],[232,158],[233,158],[234,158],[16,159],[17,159],[20,159],[21,159],[226,159],[227,159],[232,159],[233,159],[16,160],[17,160],[20,160],[21,160],[225,160],[226,160],[227,160],[231,160],[232,160],[16,161],[17,161],[18,161],[21,161],[22,161],[225,161],[226,161],[230,161],[231,161],[17,162],[18,162],[21,162],[22,162],[224,162],[225,162],[229,162],[230,162],[17,163],[18,163],[21,163],[22,163],[223,163],[224,163],[228,163],[229,163],[230,163],[17,164],[18,164],[21,164],[22,164],[223,164],[224,164],[227,164],[228,164],[229,164],[18,165],[19,165],[22,165],[23,165],[222,165],[223,165],[227,165],[228,165],[18,166],[19,166],[22,166],[23,166],[222,166],[223,166],[226,166],[227,166],[228,166],[18,167],[19,167],[22,167]];
       $point=[73,0];
       $result=$this->checkpoint($point,$points);
       echo var_dump($result);
    }



    //深度优先搜索的递归实现方法
    public function dfs($v)
    {
        //对顶点做一些操作
        echo str_repeat("-",$this->k);
        echo 'V'.($v+1).'<br>';

        //记录已访问的顶点
        $this->arr[]= $v;

        //查找与顶点相连接的顶点，如果存在就继续深度优先搜索
        for($i=0;$i<9;$i++)
        {
            if(!in_array($i,$this->arr)&&$this->dfs_save[$v][$i]==1)
            {
                $this->k++;
                $this->dfs($i);
            }
        }
        $this->k--;
        return;
    }

    public function DistanceWith($prePoint,$nextPoint){
        $distance = 0;
        for ($n=0; $n<2; $n++) {
            $difference = $prePoint[$n] - $nextPoint[$n];
            $distance  += abs($difference);
        }
       // if($distance<4){
        if($distance==0 || $distance==1 || $distance==2){
            return 1;
        }else{
            return 0;
        }
    }


    public function clustering($points){
        $this->load->library('KMeans/Kmeans');
        $clusters = Kmeans::solve($points);
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
