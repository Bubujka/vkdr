<?php
require_once 'vendor/autoload.php';

def_accessor('vkdr\app_secret');
def_accessor('vkdr\app_host');
def_accessor('vkdr\app_oauth_url');
def_accessor('vkdr\app_id');
def_accessor('vkdr\token');

def_accessor('vkdr\app_scope', array());

def_accessor('vkdr\api_url', 'https://api.vk.com/');
def_accessor('vkdr\api_version', '5.0');

def('vkdr\redirect_uri', function(){
  return vkdr\app_host().'/'.vkdr\app_oauth_url();
});

def('vkdr\oauth_url', function(){
  $p = array();
  $p['client_id'] = vkdr\app_id();
  $p['scope'] = implode(',', vkdr\app_scope());
  $p['redirect_uri'] = vkdr\redirect_uri();
  $p['response_type'] = 'code';
  $p['v'] = vkdr\api_version();
  return 'https://oauth.vk.com/authorize?'.http_build_query($p);
});

/*
Array
(
    [access_token] => xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    [expires_in] => 85690
    [user_id] => xxxxxxxxx
)
*/

def('vkdr\get_token_obj', function($code){
  $p = array();
  $p['client_id'] = vkdr\app_id();
  $p['client_secret'] = vkdr\app_secret();
  $p['redirect_uri'] = vkdr\redirect_uri();
  $p['code'] = $code;
  return json_decode(file_get_contents('https://oauth.vk.com/access_token?'.http_build_query($p)), true);
});

def_accessor('vkdr\rate_limiting', false);
def('vkdr\waaaait', function(){
  usleep(500000);
});

def('vkdr\on_error', function(){});
def('vkdr\method', function($method, $params = array()){
  // Документация по доступу к апи: http://vk.com/dev/api_requests
  if(vkdr\rate_limiting())
    vkdr\waaaait();

  $params['v'] = vkdr\api_version();
  $params['access_token'] = vkdr\token();
  $query = vkdr\api_url().'/method/'.$method.'?'.http_build_query($params);
  $r = json_decode(file_get_contents($query), true);
  if(isset($r['error']))
    return vkdr\on_error($r['error']);
  elseif(isset($r['response']))
    return $r['response'];
  
});

/*
  vkdr\send_img($url, array('file1'=>'@'.__DIR__.'/img.jpg'));
*/
def('vkdr\send_img', function($url, $post){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
});

def('vkdr\create_wrappers', function($in_global_namespace = false){
  $methods = array_map('trim', explode("\n", trim(file_get_contents(__DIR__.'/vk_methods.txt'))));
  $method_to_function_name = function($t){
    return str_replace('.', '_', $t);
  };
  foreach($methods as $m)
    def(($in_global_namespace ? '' : "vkdr\\").$method_to_function_name($m), function($params = array()) use($m){
      return vkdr\method($m, $params);
    });
  
});
vkdr\create_wrappers();

