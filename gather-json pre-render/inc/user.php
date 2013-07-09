<?php
class User {
    public static $key;
    public static $domain;

	public function getPlayer($id) {
        $response = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$this->key.'&steamids='.$id);
        $json = json_decode($response);
        return $json->response->players[0];
    }

	public function login() {
		require_once 'openid.php';
		$openid = new LightOpenID($this->domain);
		if (!$openid->mode) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: '.$openid->authUrl());
		} elseif ($openid->mode == 'cancel') {
            echo 'User has canceled authentication!';
        } else {
            if ($openid->validate()) {
				preg_match("/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/", $openid->identity, $matches); // steamID: $matches[1]
				setcookie('steamID', $matches[1], time()+(60*60*24*7), '/'); // 1 week
				header('Location: /gather');
				exit;
			}
        }
    }
}
?>