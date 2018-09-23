<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class  Titles_Model extends CI_Model{

	const TBL_WEBBEASE = 'titlesInfo';

   public function addTitleinfo($data)
	{
		$query=$this->db->insert(self::TBL_WEBBEASE,$data);
		return $this->db->insert_id();
	}
	public function findTitle($clipXNo,$clipYNo){
		$this->db->where('clipX',$clipXNo);
		$this->db->where('clipY',$clipYNo);
		$query = $this->db->get(self::TBL_WEBBEASE);
		return $query->first_row();
	}

	public function findTitleById($id){
        $this->db->where('id',$id);
        $query = $this->db->get(self::TBL_WEBBEASE);
        return $query->first_row();
    }

	public function findAllTiles(){
        $this->db->where('zoomlevel',17);
        $this->db->from(self::TBL_WEBBEASE);
        $query = $this->db->get();
        return $query->result();
    }

    public function uncheckedTiles(){
        $this->db->where('is_checked',0);
        $this->db->from(self::TBL_WEBBEASE);
        $query = $this->db->get();
        return $query->result();
    }
    public function findFistUncheckedTitle(){
        $this->db->where('is_checked',0);
        $this->db->from(self::TBL_WEBBEASE);
        $query = $this->db->get();
        return $query->first_row();
    }

    public function saveTileStatus($id){
        $this->db->where('id',$id);
        $data['is_checked']=1;
        return $this->db->update(self::TBL_WEBBEASE,$data);
    }
}
