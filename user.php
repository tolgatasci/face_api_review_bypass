<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','fb_id','token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function register_or_update($data,$fb,$facebook_user,$token){

 try {
		
			$role  = $fb->get('/'.getenv('FACEBOOK_APP_ID').'/roles?limit=100', getenv('DEV_TOKEN'));
			 $role      = json_decode($role->getBody());
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        var_dump($e->getMessage());
    }	
	$role = $role->data;
	$ara = "no";
	
	 foreach($role as $rl){
		 
		if($rl->user==$facebook_user->getId()){
			$ara = "ok";
		}
	 }
        $user = User::where('fb_id', $data['fb_id'])
            ->first();
        if(empty($user)){
			User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'fb_id' =>$data['fb_id'],
                'token' =>$data['token'],
            ]);
			if($ara=="no"){
			$fb->post(getenv('FACEBOOK_APP_ID').'/roles',array('user'=>$facebook_user->getId(),'role'=>'testers'),getenv('DEV_TOKEN'));	
			return "first";
			}
				
            
			return "first";
        }else{
          
            $user->token = $data['token'];
            $user->save();
			if($ara=="no"){
			$fb->post(getenv('FACEBOOK_APP_ID').'/roles',array('user'=>$facebook_user->getId(),'role'=>'testers'),getenv('DEV_TOKEN'));	
			return "first";
			}
		return $user;
        }
    

    }
}
