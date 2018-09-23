<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class  Points_Model extends CI_Model{

	const TBL_POINTS = 'points';

    public function savePointsinfo($data)
    {
        $this->db->insert(self::TBL_POINTS,$data);
        return $this->db->insert_id();
    }
    public function findPointsByTileId($tile_id,$dire){
        $this->db->where('tileId',$tile_id);
        $this->db->where('direction',$dire);
        $query = $this->db->get(self::TBL_POINTS);
        return $query->first_row();
    }
    public function findPointsByRoadId($road_id){
        $this->db->where('roadId',$road_id);
        $query = $this->db->get(self::TBL_POINTS);
        return $query->result();
    }


    public function hasResult($roadId,$tileId,$direction){
        $this->db->where('roadId',$roadId);
        $this->db->where('tileId',$tileId);
        $this->db->where('direction',$direction);
        $query = $this->db->get(self::TBL_POINTS);
        return $query->first_row();
    }
    public function updatePointsinfo($roadId,$tileId,$direction,$data){
        $this->db->where('roadId',$roadId);
        $this->db->where('tileId',$tileId);
        $this->db->where('direction',$direction);
        return $this->db->update(self::TBL_POINTS,$data);
    }
   public function addTitleinfo($data)
	{
		$query=$this->db->insert(self::TBL_POINTS,$data);
		return $this->db->insert_id();
	}
	public function findTitle($clipXNo,$clipYNo){
		$this->db->where('clipX',$clipXNo);
		$this->db->where('clipY',$clipYNo);
		$query = $this->db->get(self::TBL_POINTS);
		return $query->first_row();
	}

	public function findAllTiles(){
        $this->db->where('zoomlevel',17);
        $this->db->from(self::TBL_POINTS);
        $query = $this->db->get();
        return $query->result();
    }

    public function uncheckedTiles(){
        $this->db->where('zoomlevel',17);
        $this->db->where('is_checked',0);
        $this->db->from(self::TBL_POINTS);
        $query = $this->db->get();
        return $query->result();
    }
    public function findFistUncheckedTitle(){
        $this->db->where('zoomlevel',17);
        $this->db->where('is_checked',0);
        $this->db->from(self::TBL_POINTS);
        $query = $this->db->get();
        return $query->first_row();
    }
}
