<?php



/**
*  Copyright 2014 Orkiv LLC
*/

class InvManager
{
    public $credentials;
    public $apiurl = "https://orkiv.com/i/ext_api.php";

    function __construct($argument)
    {
        # code...
        //array of coordinates
        $this->credentials = $argument;

    }
    public function __call($methodName, $params = null)
    {
            # code...
            if($methodName == "write"){
            ////pagination, category,array query,pmin,pmax
             $request = array("write" => true);
             $request["callURL"] = $params[0];
             $request["toService"] = $params[1];
             $request["jsonData"] = json_encode($params[2]);
            

            }
            else if($methodName == "keyset"){
                 $request = array("keyset" => true);
                 $request["field"] = $params[0];
              
            }
            else if($methodName == "query"){
                //[0] page 1 cat ,2 min price, 3 max , query
                $request = array("query" => true,"pmin" => $params[2],"pmax" => $params[3], "category" => $params[1], "pagination" => $params[0]);
                foreach ($params[4] as $key => $value) {
                    # code...
                    $request[$key] = $value;
                }
            }
            else if ($methodName == "add"){
                $request["add"] = $params[0];
                foreach ($params[1] as $key => $value) {
                    # code...
                    $request[$key] = $value;
                }
            }
            else if ($methodName == "update"){
                $request["update"] = $params[0];
                foreach ($params[1] as $key => $value) {
                    # code...
                    $request[$key] = $value;
                }
            }
            else if($methodName == "delete"){
                $request["remove"] = $params[0];
            }
            else if ($methodName == "order"){
                    $request =  array('open' => $methodName );
                    $request['order'] = $params[0];
            }
            else if ($methodName == "orders"){
                    $request =  array('open' => $methodName );
                  //  print_r($params[0]);
                    if( is_array( $params[0] ) )
                    $request['json_query'] = json_encode($params[0]);
            }
            else { $request =  array('open' => $methodName );
            if(count($params) == 1) 
                $request['open'] = $params[0];
            }
            return $this->process($request);    
        
    }

    public function process($request){
        if(!isset($request['key']))
        $request['key'] = $this->credentials['key'];
        $request['id'] = $this->credentials['id'];
        //account ID
      //  echo "Sending";
       //print_r($request);
        $postdata = http_build_query($request);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);

        return json_decode(file_get_contents($this->apiurl, false, $context));  

        }

    

}

?>
