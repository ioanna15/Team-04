<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracker extends CI_Controller {


		public function mainpage()
	{
        if($this->session->userdata("username") != "")
        {
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
            {
                $est_id = $this->session->userdata("entered");
                redirect("tracker/establishment_entry/$est_id");
            }
        
            $user = $this->session->userdata("username"); 
                $this->load->view("mainpage", array
            (
                "username" => $user,
            ));

        }
        else{
            redirect("tracker/login");
        }
	}


    

    public function login()
    {  
          if($this->session->userdata("username") != "")
        {
            redirect("tracker");
        }
        unset($_SESSION['not_equal']);
        $this->load->view("login/login");
    }

    public function login_auth()
    {
        unset($_SESSION['registered']);
        unset($_SESSION['wrong']); // use to clear flash data
        $config_rules = array(
            array (
                "field" => "username_txt",
                "label" => "Username",
                "rules" =>"trim|required",  // callback_ use to call any function you want
            ),
             array(
                "field" => "password_txt",
                "label" => "Password",
                "rules" =>"trim|required",
            ),  
        );
        $this->form_validation->set_rules($config_rules);
        if($this->form_validation->run() == false){

            $this->login(); // run this function again
        }
        else
        {
            $username = $this->input->post("username_txt");
            $password = $this->input->post("password_txt");

            if($this->t_model->login_validation($username,$password))
            {
                $this->session->set_userdata("username",$username);
                if($this->session->userdata("currentpage") != "")
                {
                    redirect($this->session->userdata("currentpage"));
                }
                redirect("tracker");
            }
            else{
                $this->session->set_flashdata("wrong", "Invalid Credentials");
                $this->login();
            }
            }
        
    }


    public function logout()
    {
        $this->session->unset_userdata("username");
        redirect("tracker/login");

    }

    public function register()
    {
           if($this->session->userdata("username") != "")
        {
            redirect("tracker");
        }
        unset($_SESSION['registered']);
        unset($_SESSION['wrong']);
        $this->load->view("register/register");
    }


    public function register_auth()
    {
        unset($_SESSION['wrong']);
         unset($_SESSION['not_equal']);
           $config_rules = array(
            array (
                "field" => "username_txt",
                "label" => "Username",
                "rules" =>"trim|required|is_unique[user.username]",
            ),
             array(
                "field" => "password1_txt",
                "label" => "Password",
                "rules" =>"trim|required|min_length[8]|max_length[20]",
            ),   
            array(
                "field" => "password2_txt",
                "label" => "Confirm Password",
                "rules" =>"trim|required|min_length[8]|max_length[20]",
            ),  
        );

        $this->form_validation->set_rules($config_rules);

        if($this->form_validation->run() == false)
        {
            $this->register();
        }
        else
        {
            $username = $this->input->post("username_txt");
            $password1 = $this->input->post("password1_txt");
            $password2 = $this->input->post("password2_txt");

            if($password1 != $password2)
            {
                $this->session->set_flashdata("not_equal", "Your passwrods are not the same");
                $this->register();
            }
            else
            {
                   
                $this->t_model->register_credentials(
                    array(
                        "username" => $username,
                        "password" => $this->encryption->encrypt($password1),
                    )
                    );
                $this->session->set_flashdata("registered", "You succesfully registered!");
                redirect("tracker/login");
                
            }
        }

    }

    // public function createEstablishment() {
    //     if($this->session->userdata("username") != "")
    //         {   
    //                 if($this->session->userdata("entered")!= "")
    //             {
    //                 $est_id = $this->session->userdata("entered");
    //                 redirect("tracker/establishment_entry/$est_id");
    //             }
        
    //             $this->load->view('establishment/createEst');
    //        }
    //     else
    //         {
    //             redirect("tracker/login");
    //         }

    // }

    public function user_profile()
    {
        if($this->session->userdata("username") != "")
        {   
            if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
        
            $user = $this->session->userdata("username"); 
            $user_id = $this->t_model->get_user_id($user);
            if($this->t_model->is_user_have_ct($user_id))
            {
                redirect('tracker/contact_tracing');
            }
            else
            {
                redirect("tracker/contact_tracing_form");
            }

        }
        else{
            redirect("tracker/login");
        }

    }

    public function contact_tracing_form()
    {
          if($this->session->userdata("username") != "")
        {   
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
            $this->load->view('contact_tracing/contact_t_form');
        }
        else{
            $this->session->set_userdata("currentpage","tracker/contact_tracing_form");
            redirect("tracker/login");
        }
    }

    public function contact_tracing_auth()
    {
        $config_rules = array(
            array (
                "field" => "firstname_txt",
                "label" => "Firstname",
                "rules" =>"trim|required|min_length[3]|max_length[20]",
            ),
                array (
                "field" => "lastname_txt",
                "label" => "Lasstname",
                "rules" =>"trim|required|min_length[2]|max_length[20]",
            ),
                array (
                "field" => "phone_txt",
                "label" => "PhoneNumber",
                "rules" =>"trim|required|min_length[11]|max_length[20]",
            ),
                array (
                "field" => "age_txt",
                "label" => "Age",
                "rules" =>"trim|required|min_length[2]|max_length[3]",
            ),
                array (
                "field" => "email_txt",
                "label" => "Email",
                "rules" =>"trim|required|min_length[8]|max_length[30]",
            ),
        );

        $this->form_validation->set_rules($config_rules);

        if($this->form_validation->run() == false)
        {
            $this->contact_tracing_form();
        }
        else
        {
            $user = $this->session->userdata("username"); 
            $firstname = $this->input->post("firstname_txt");
            $lastname = $this->input->post("lastname_txt");
            $phone = $this->input->post("phone_txt");
            $email = $this->input->post("email_txt");
            $age = $this->input->post("age_txt");
            // add_contract_tracing
            $this->t_model->add_contract_tracing(
                        array(
                            "user_id" =>$this->t_model->get_user_id($user),
                            "first_name" => $firstname,
                            "last_name" => $lastname,
                            "age" => $age,
                            "phone_number" => $phone,
                            "email" => $email,
                        )
                        );
            redirect("tracker/contact_tracing");
        }
    }

    public function contact_tracing()
    {
        if($this->session->userdata("username") != "")
            {
                $this->session->unset_userdata('currentpage');
                if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
                $user = $this->session->userdata("username"); 
                $user_id = $this->t_model->get_user_id($user);
                $data = $this->t_model->get_user_ct($user_id);
                $this->load->view('contact_tracing/contact_t',array(
                    "data"=>$data,
                ));
            }
        else{
            $this->session->set_userdata("currentpage","tracker/contact_tracing");
            redirect("tracker/login");
        }

    }

    public function contact_tracing_update($id)
    {
        if($this->session->userdata("username") != "")
        {
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
            $data = $this->t_model->get_ct_by_id($id);
            $this->load->view("contact_tracing/contact_t_update",array(
                "data" => $data,
            ));
        }
        else{
            $this->session->set_userdata("currentpage","tracker/contact_tracing_update/$id");
            redirect("tracker/login");
        }
    }

    public function contact_tracing_update_auth($ct_id)
    {
        $config_rules = array(
            array (
                "field" => "firstname_txt",
                "label" => "Firstname",
                "rules" =>"trim|required|min_length[3]|max_length[20]",
            ),
                array (
                "field" => "lastname_txt",
                "label" => "Lasstname",
                "rules" =>"trim|required|min_length[2]|max_length[20]",
            ),
                array (
                "field" => "phone_txt",
                "label" => "PhoneNumber",
                "rules" =>"trim|required|min_length[11]|max_length[20]",
            ),
                array (
                "field" => "age_txt",
                "label" => "Age",
                "rules" =>"trim|required|min_length[2]|max_length[3]",
            ),
                array (
                "field" => "email_txt",
                "label" => "Email",
                "rules" =>"trim|required|min_length[8]|max_length[30]",
            ),
        );

        $this->form_validation->set_rules($config_rules);

        if($this->form_validation->run() == false)
            {
                 $this->contact_tracing_update($ct_id);

            }

        else
            {
                $firstname = $this->input->post("firstname_txt");
                $lastname = $this->input->post("lastname_txt");
                $phone = $this->input->post("phone_txt");
                $email = $this->input->post("email_txt");
                $age = $this->input->post("age_txt");
                $data= array(
                    "first_name" => $firstname,
                    "last_name" => $lastname,
                    "age" => $age,
                    "phone_number" => $phone,
                    "email" => $email,

                );
                $this->t_model->update_ct($ct_id,$data);

                redirect("tracker/contact_tracing");

            }   

    }

    //Establishment Create
    public function Establishment_Create() {
        if($this->session->userdata("username") != "")
        {   
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                    {
                        $est_id = $this->session->userdata("entered");
                        redirect("tracker/establishment_entry/$est_id");
                    }
            $this->load->view('establishment/Establishment_C');
         }
        else{
            $this->session->set_userdata("currentpage","tracker/Establishment_Create");
            redirect("tracker/login");
        }

    }

    // public function user_prof_este()
    // {
    //     if($this->session->userdata("username") != "")
    //     {   
    //          if($this->session->userdata("entered")!= "")
    //             {
    //                 $est_id = $this->session->userdata("entered");
    //                 redirect("tracker/establishment_entry/$est_id");
    //             }
    //         $user = $this->session->userdata("username"); 
    //         $user_id = $this->t_model->get_user_id($user);
    //         if($this->t_model->is_user_have_ctt($user_id))
    //         {
    //             redirect('tracker/Establishment_Create');
    //         }
    //         else
    //         {
    //             redirect("tracker/Establishment_Create");
    //         }

    //     }
    //     else{
    //         redirect("tracker/login");
    //     }

    // }

    public function Establishment_auth() {
        $config_rules = array(
            array (
                "field" => "name_txt",
                "label" => "Name",
                "rules" =>"trim|required|min_length[1]|max_length[200]",
            ),
                array (
                "field" => "location_txt",
                "label" => "Location",
                "rules" =>"trim|required|min_length[1]|max_length[100]",
            ),
                array (
                "field" => "description_txt",
                "label" => "Description",
                "rules" =>"trim|required|min_length[1]|max_length[200]",
            ),
        );

        $this->form_validation->set_rules($config_rules);

        if($this->form_validation->run() == false)
        {
            $this->Establishment_Create();
        }
        else
        {
            $user = $this->session->userdata("username"); 
            $name = $this->input->post("name_txt");
            $location = $this->input->post("location_txt");
            $description = $this->input->post("description_txt");
            // add_contract_tracing
            $this->t_model->add_establishment_try(
                        array(
                            "userID" =>$this->t_model->get_user_id($user),
                            "name" => $name,
                            "location" => $location,
                            "description" => $description,
                        )
                        );
             redirect("tracker/displayEstab");
        }
    }

    public function MyEstablishments() {
       

        if($this->session->userdata("username") != "")
        {
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
            $user = $this->session->userdata("username"); 
            $user_id = $this->t_model->get_user_id($user);
            $data = $this->t_model->get_user_establishment($user_id);
            $this->load->view('establishment/Establishmentown',array(
                "data"=>$data,
                "username" => $user,
            ));
        }
    else{
        $this->session->set_userdata("currentpage","tracker/MyEstablishments");
        redirect("tracker/login");
    }
    }

    public function Establishment_specific($establishment_id) {
        
        if($this->session->userdata("username") != "")
        {   
            $this->session->unset_userdata('currentpage');
           if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                } 
           $username = $this->session->userdata("username");
           $data = $this->t_model->get_establishment_by_id($establishment_id);
            $this->load->view('establishment/establishment_specific', array(
                "data" =>$data,
                "userid" => $this->t_model->get_user_id($username)
            )) ;
            
        }
        else{
            $this->session->set_userdata("currentpage","tracker/Establishment_specific/$establishment_id");
            redirect("tracker/login");
        }
    }



    //Establishment Update

    public function Establishment_update($id)
    {
        if($this->session->userdata("username") != "")
        {
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                {
                    $est_id = $this->session->userdata("entered");
                    redirect("tracker/establishment_entry/$est_id");
                }
            $data = $this->t_model->get_establishment_by_id($id);
            $this->load->view("establishment/establishment_update",array(
                "data" => $data,
            ));
        }
        else{
            $this->session->set_userdata("currentpage","tracker/Establishment_update/$id");
            redirect("tracker/login");
        }
    }

    public function este_update_logic($este_id)
    {
        $config_rules = array(
            array (
                "field" => "name_txt",
                "label" => "name",
                "rules" =>"trim|required|min_length[3]|max_length[100]",
            ),
                array (
                "field" => "location_txt",
                "label" => "location",
                "rules" =>"trim|required|min_length[2]|max_length[100]",
            ),
                array (
                "field" => "description_txt",
                "label" => "description",
                "rules" =>"trim|required|min_length[3]|max_length[500]",
            ),
        );

        $this->form_validation->set_rules($config_rules);

        if($this->form_validation->run() == false)
            {
                 $this->Establishment_update($este_id);

            }

        else
            {
                $name = $this->input->post("name_txt");
                $location = $this->input->post("location_txt");
                $description = $this->input->post("description_txt");
                $data= array(
                    "name" => $name,
                    "location" => $location,
                    "description" => $description,

                );
                $this->t_model->update_establishment($este_id,$data);

                redirect("tracker/MyEstablishments");

            }   

    }

    // DISPLAY ALL ESTABLISHMENT
    public function display_establishment()
    {
        if($this->session->userdata("username") != "")
        {
            $this->session->unset_userdata('currentpage');
            $this->session->unset_userdata('entered');
            $list = $this->t_model->get_all_establishments();
            $this->load->view("establishment/display_establishments", array(
                "establishments" => $list,
            ));
        }
        else{
            $this->session->set_userdata("currentpage","tracker/display_establishment");
            redirect("tracker/login");
        }
    }
    //add ka lang ng dates
    public function establishment_entry($est_id)
    {
        if($this->session->userdata("username") != "")
        {
            
            $this->session->unset_userdata('currentpage');
            if($this->session->userdata("entered")!= "")
                {
                        $this->load->view("report/entered", array(
                        'est_id' => $est_id,
                    ));
                }
            else
            {
                $user_id = $this->t_model->get_user_id($this->session->userdata("username"));
                $user_ct_id = $this->t_model->get_user_ct_by_id($user_id);
                if(empty($user_ct_id))
                    {
                        redirect("tracker/contact_tracing_form");
                    }
                if($this->t_model->is_report_not_inside($user_ct_id,$est_id))
                    {
                        $this->t_model->add_report(array(
                        "ct_id"=> $user_ct_id,
                        " est_id"=> $est_id
                        ));
                    }
                $this->session->set_userdata("entered",$est_id);
                $this->load->view("report/entered", array(
                        'usserid' => $est_id,
                        "ct_id" => $user_ct_id,

                    ));
            
            }
        }
        else
            {
                $this->session->set_userdata("currentpage","tracker/Establishment_entry/$est_id");
                redirect("tracker/login");
            }
    
    }



}

