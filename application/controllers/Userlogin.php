<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userlogin extends CI_Controller {

    public function login(){
        $this->load->view("login.html");
    }
    public function usersignin(){
	    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $admin_name = $this->input->post('username',true);
        $password = $this->input->post('passwd',true);
        $pwdmd5=MD5($password);
        $this->load->model("User_Model");
        $user = $this->User_Model->login($admin_name,$pwdmd5);
        //var_dump($user);
        if($user!=null)
        {
            $this->session->set_userdata('user',$admin_name);
            $this->session->set_userdata('userid',$user->id);
            $this->session->set_userdata('pwd',$pwdmd5);
            $this->session->set_userdata('role',$user->role);
            if($user->role==0)//1为admin 0为user
            {
                echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=../drawroute/roadlist\" >";
            }else{
                echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=../drawroute/roadlist\" >";
            }
        }else {
            echo "<script>alert('用户名或密码错误！'); history.go(-1);</script>";
        }
	}

    public function signup(){
        $this->load->model('User_Model');
        $data['username'] = $this->input->get('username');
        $userpwd = $this->input->get('password');
        $data['role'] = $this->input->get('role');
        $data['status'] = 1;
        $data['create_time'] = strtotime(date('Y-m-d H:i:s'));
        $data['password'] = MD5($userpwd);
        $re = $this->User_Model->addAdmin($data);
        if($re){
           echo "增加成功";
        }else{
            echo "增加失败";
        }
    }

	function generate_code($length = 4) {
	    return rand(pow(10,($length-1)), pow(10,$length)-1);
	}

	public function signout(){
        $this->load->model('Route_Model');
        $this->load->model("User_Model");
        $road = $this->input->get("roadid");
        $userid=$this->session->userdata('userid');
//        $data["last_road"]=$road;
//        $this->User_Model->updataAdmin($userid,$data);
        $dataroute["locking"]=0;
        $save_id = $this->Route_Model->updateRoad($road,$dataroute);
	    $this->session->unset_userdata('vipusername');
	    $this->session->unset_userdata('vipuserpwd');
	    $this->session->unset_userdata('vipuserid');
		//$this->session->sess_destroy();
        echo true;
	    //redirect('/userlogin/login');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
