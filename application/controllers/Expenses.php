<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends CI_Controller
{ 
    public function __construct()
    {
        parent::__construct();
        headersUp();
        
    }

    public function index()
    {
        try 
		{
            $resp["payload"] = $this->expense->get_all();
            $resp["status"]  = 200;

			return response($resp["status"], $resp["payload"]);
		}
        catch (Exception $e) 
		{ 
			return response($e->getCode(),$e->getMessage());
		}
    }

    public function create()
	{
		try 
		{
			$data = $this->input->raw_input_stream;
			$data = utf8_encode($data);
            $data = json_decode($data, true);
            
            //flatten the array, because CI form validation library has issues reading multidimensional array
            $out   = [];
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
            foreach($iterator as $key => $val)
            {
                $out[$key] = $val;
            }
            $this->form_validation->set_data($out);
            $this->form_validation->set_rules('product_name', 'Product Name', 'required');
            $this->form_validation->set_rules('quantity', 'Quantity', 'required|callback_quantity_isNot_zero');
            $this->form_validation->set_rules('product_price', 'Product Price', 'required');

			if ($this->form_validation->run() == FALSE)
			{
						$errorMessage = $this->form_validation->error_string();
						$errorStatus = 400;
						throw new Exception($errorMessage, $errorStatus);
			}

			
			$resp["payload"] = $this->expense->create($data);
			$resp["status"]  = 200;
	
			return response($resp["status"], $resp["payload"]);
		} 
        catch (Exception $e) 
		{ 
			return response($e->getCode(),$e->getMessage());
		}
    }
    
	public function quantity_isNot_zero($quantity)
	{
			$this->form_validation->set_message('quantity_isNot_zero', 'Product quantity can\'t be zero ');

			if(!$this->product->quantity_isNot_zero($quantity))
			{
				return false;
			}else{

				return true;
			}

	}
}
