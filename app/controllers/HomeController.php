<?php 
class HomeController extends Controller
{
    public function index()
    {
        $data=array(
            "title"=>"lalala",
            "data"=>"lalala32"
        );          
        
        $this->view("home2",$data);
    }    
}
