# Facebook api permissions fail or bug bypass
That's the logic. We're adding a test user to the user. If you do not accept, we refuse access.

$role  = $fb->get('/'.getenv('FACEBOOK_APP_ID').'/roles?limit=100', "APP TOKEN");
$role      = json_decode($role->getBody());

$ara = "no";
	
	 foreach($role as $rl){
		 
		if($rl->user==USER_ID){
			$ara = "ok";
		}
	 }
   
   if($ara=="no"){
			$fb->post(getenv('FACEBOOK_APP_ID').'/roles',array('user'=>$facebook_user->getId(),'role'=>'testers'),getenv('DEV_TOKEN'));	
			return "first";
			}
      
  if return first Again LOGİN and response info
  
  plase sending application accep plase and login.
