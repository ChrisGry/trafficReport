<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class  User_Model extends CI_Model{

	const TBL_ADMIN = 'user';

    public function getAdminById($id)
	{
		 $this->db->where('id', $id);
		 $query = $this->db->get(self::TBL_ADMIN);
		 return $query->first_row();
	}
    public function checkUserPwd($id,$pwd)
	{
		 $this->db->where('id', $id);
		 $this->db->where('password', $pwd);
		 $query = $this->db->get(self::TBL_ADMIN);
		 return $query->num_rows() > 0 ? true : false;
	}
	public function login($user,$password)
	{
	     $this->db->where('status',1);
		 $this->db->where('username', $user);
		 $this->db->where('password', $password);
		 $query = $this->db->get(self::TBL_ADMIN);
		 return $query->first_row();
	}
    public function updatePassword($id,$data)
	{
	     $condition['id'] = $id;
		 return $this->db->where($condition)->update(self::TBL_ADMIN,$data);
	}
	
	
	public function getAllSys(){
        $query = $this->db->get(self::TBL_ADMIN);
        return $query->result();
	}
	public function getAllSysUpdata($id){
	    $this->db->where('id !=',$id);
	    $query = $this->db->get(self::TBL_ADMIN);
	    return $query->result();
	}
	public function getSysLimitList($num,$offset)
	{
	    $this->db->where('status',1);
	    $this->db->order_by('order', 'asc');
	    $this->db->limit($num,$offset);
	    $query = $this->db->get(self::TBL_ADMIN);
	    return $query->result();
	}
	public function getSysLimitListAc()
	{
	    $this->db->where('status',1);
	    $this->db->order_by('order', 'asc');
	    $query = $this->db->get(self::TBL_ADMIN);
	    return $query->num_rows();
	}
	public function getSysById($id){
	    $this->db->where('id',$id);
	    $query = $this->db->get(self::TBL_ADMIN);
	    return $query->first_row();
	}
	public function addAdmin($data){
	    return $this->db->insert(self::TBL_ADMIN,$data);
	}
	public function updataAdmin($id,$data){
	    $this->db->where('id',$id);
	    return $this->db->update(self::TBL_ADMIN,$data);
	}
	public function deleteSysData($id){
	    $this->db->where('id',$id);
	    return $this->db->delete(self::TBL_ADMIN);	    
	}
	
}