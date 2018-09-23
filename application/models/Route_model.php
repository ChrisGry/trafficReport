<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class  Route_Model extends CI_Model{

	const TBL_ROUTE = 'route';
    const TBL_ROUTE_SEQ = 'croute';
    const TBL_ROAD_TILE = 'road_tile';

    public function saveRouteinfo($data)
    {
        $this->db->insert(self::TBL_ROUTE,$data);
        return $this->db->insert_id();
    }
    public function updateRoad($id,$data)
    {
        $this->db->where('roadId',$id);
        return $this->db->update(self::TBL_ROUTE,$data);
    }

    public function saveRoadTile($data)
    {
        $this->db->insert(self::TBL_ROAD_TILE,$data);
        return $this->db->insert_id();
    }
    public function updateRoadTile($id,$data)
    {
        $this->db->where('id',$id);
        return $this->db->update(self::TBL_ROAD_TILE,$data);
    }

    public function checkedRoad($id,$data){
        $this->db->where('roadId',$id);
        return $this->db->update(self::TBL_ROUTE,$data);
    }

	public function getRouteByName($Name){
		$this->db->where('routeName',$Name);
		$query = $this->db->get(self::TBL_ROUTE);
		return $query->first_row();
	}

	public function findAllRoute(){
        $this->db->from(self::TBL_ROUTE);
        $query = $this->db->get();
        return $query->result();
    }
    public function findRouteByRoadId($id){
        $this->db->where('roadId',$id);
        $this->db->from(self::TBL_ROUTE);
        $query = $this->db->get();
        return $query->first_row();
    }

    public function findRoadTileByRoadId($id,$direction){
        $this->db->where('roadId',$id);
        $this->db->where('direction',$direction);
        $this->db->from(self::TBL_ROAD_TILE);
        $query = $this->db->get();
        return $query->first_row();
    }

    public function uncheckedTiles(){
        $this->db->where('zoomlevel',17);
        $this->db->where('is_checked',0);
        $this->db->from(self::TBL_ROUTE);
        $query = $this->db->get();
        return $query->result();
    }
    public function findFistUncheckedRoute(){
//        $this->db->where('locking',0);
        $this->db->where('checked',0);
        $this->db->from(self::TBL_ROUTE);
        $query = $this->db->get();
        return $query->first_row();
    }
    public function findAllCheckedRoadLimit($num,$offset){
        $this->db->where('checked',1);
        $this->db->from(self::TBL_ROUTE);
        $this->db->limit($num,$offset);
        $query = $this->db->get();
        return $query->result();
    }
    public function getCheckedRoadAmount(){
        $this->db->where('checked',1);
        $this->db->from(self::TBL_ROUTE);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function findRouteSeqByRoadId($roadId){
        $this->db->where('checked',0);
        $this->db->where('roadId',$roadId);
        $this->db->from(self::TBL_ROUTE_SEQ);
        $query = $this->db->get();
        return $query->result();
    }

}
