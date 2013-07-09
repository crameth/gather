<?php
class Gather {
	public static $data;
	public static $gather_id;
	
	function __construct($id) {
		$p = './gathers/'.$id.'.json';
		$f = file_get_contents($p);
		$this->data = json_decode($f, true);
	}
	public function read($id) {
		$p = './gathers/'.$id.'.json';
		$f = file_get_contents($p);
		$this->data = json_decode($f, true);
		return $this->data;
	}
	public function write($id) {
		$p = './gathers/'.$id.'.json';
		$d = json_encode($this->data);
		$f = fopen($p, 'w');
		fwrite($f, $d);
		fclose($f);
		
		$this->compile($id);
		
		return true;
	}
	
	public function getGameServer($type = null) {
		$server = $this->data['gather']['game_server'];
		
		switch ($type) {
			case 'ip':
				$ip = explode(':', $server);
				return $ip[0];
			break;
			case 'port':
				$port = explode(':', $server);
				if ( isset($port[1]) ) {
					$port = explode(' ', $port[1]);
					if ( isset($port[0]) ) {
						return $port[0];
					} else {
						return '';
					}
				} else {
					return '';
				}
			break;
			case 'password':
				$password = explode(' ', $server);
				if ( isset($password[1]) ) {
					return $password[1];
				} else {
					return '';
				}
			break;
			default:
				return $server;
		}
	}
	public function setGameServer($ip, $port, $password) {
		$server = $this->validate($ip, $port, $password);
		
		$this->data['gather']['game_server'] = $server;
	}
	
	public function getVoiceServer($type = null) {
		$server = $this->data['gather']['voice_server'];
		
		switch ($type) {
			case 'ip':
				$ip = explode(':', $server);
				return $ip[0];
			break;
			case 'port':
				$port = explode(':', $server);
				if ( isset($port[1]) ) {
					$port = explode(' ', $port[1]);
					if ( isset($port[0]) ) {
						return $port[0];
					} else {
						return '';
					}
				} else {
					return '';
				}
			break;
			case 'password':
				$password = explode(' ', $server);
				if ( isset($password[1]) ) {
					return $password[1];
				} else {
					return '';
				}
			break;
			default:
				return $server;
		}
	}
	public function setVoiceServer($ip, $port, $password) {
		$server = $this->validate($ip, $port, $password);
		
		$this->data['gather']['voice_server'] = $server;
	}
	
	public function voiceType($voice_type = null) {
		if ( isset($voice_type) ) {
			// 0: mumble
			// 1: teamspeak
			switch ($voice_type) {
				case 0:
					$voice_type = 'mumble';
				break;
				case 1:
					$voice_type = 'teamspeak';
				break;
				default:
					return false;
					exit;
			}
			$this->data['gather']['voice_type'] = $voice_type;
			
			return true;
		} else {
			return $this->data['gather']['voice_type'];
		}
	}
	
	public function status($status = null) {
		if ( isset($status) ) {
			$this->data['gather']['status'] = $status;
			
			return true;
		} else {
			return $this->data['gather']['status'];
		}
	}
	
	public function getNumPlayers() {
		$players = $this->data['players'];
		$count = count($players);
		
		return $count;
	}
	public function getPlayer($id) {
		$players = $this->data['players'];
		
		foreach($players as $player) {
			if ( $player['id'] === $id ) {
				return $player;
			}
		}
		
		return false;
	}
	public function getAllPlayers() {
		return $this->data['players'];
	}
	public function addPlayer($id, $name, $avatar) {
		$players = $this->data['players'];
		$maps = $this->data['maps'];
		
		$num_players = count($players);
		$num_maps = count($maps);
		
		// check if gather already has 12 players
		if ( $this->data['gather']['status'] != 'wait' ) {
			header('Location: /gather');
		}
		
		// check if player is already in gather
		foreach ($players as $player) {
			if ( $player['id'] === $id ) {
				setcookie('gather', $this->data['gather']['id'], time()+(60*60*24*7), '/');
				
				die('Player is already in gather');
			}
		}
		
		$this->data['players'][] = array( 'id'=>(string)$id, 'name'=>(string)$name, 'avatar'=>(string)$avatar, 'map_vote'=>(int)$num_maps, 'captain_vote'=>12, 'votes'=>0 );
		
		// set cookie
		setcookie('gather', $this->data['gather']['id'], time()+(60*60*24*7), '/');
		
		// recount to check if 12 players have been reached
		$players = $this->data['players'];
		$num_players = count($players);
		
		if ( $this->data['gather']['status'] == 'wait' && $num_players > 11 ) {
			$this->data['gather']['status'] = 'vote';
			
			// for creating new gather files
			return true;
		} else {
			return false;
		}
	}
	public function removePlayer($id) {
		$players = $this->data['players'];
		$maps = $this->data['maps'];
		
		$num_players = count($players);
		$num_maps = count($maps);
		
		// check if gather already has 12 players
		if ( $this->data['gather']['status'] != 'wait' || $num_players > 11 ) {
			setcookie('gather', '', -1, '/');
			
			return true;
		} else {
			$b = false;
			
			foreach ($players as $key => $player) {
				if ( $player['id'] === $id ) {
					unset($this->data['players'][$key]);
					$b = true;
				}
			}
			
			if (!$b) {
				return false;
				exit;
			}
			
			// kill cookie
			setcookie('gather', '', -1, '/');
			
			$this->reindex($this->data['players']);
			
			return true;
		}
	}
	
	public function getMap($id) {
		return $this->data['maps'][$id];
	}
	public function getAllMaps() {
		return $this->data['maps'];
	}
	
	public function captainVote($id, $captain = null) {
		if ( isset($captain) ) {
			$this->data['players'][$id]['captain_vote'] = $captain;
			
			return true;
		} else {
			return $this->data['players'][$id]['captain_vote'];
		}
		
		$this->tallyVote();
	}
	public function mapVote($id, $map = null) {
		if ( isset($map) ) {
			$this->data['players'][$id]['map_vote'] = $map;
			
			return true;
		} else {
			return $this->data['players'][$id]['map_vote'];
		}
		$this->tallyVote();
	}
	private function tallyVote() {
		$players = $this->data['players'];
		$maps = $this->data['maps'];
		
		$num_players = count($players);
		$num_maps = count($maps);
		
		$cap_votes = array_fill(0,12,12);
		$map_votes = array_fill(0,$num_maps,0);
		
		$num_captain_votes = 0;
		
		foreach($players as $player) {
			if ($player['map_vote'] < $num_maps) {
				$map_votes[$player['map_vote']] += 1;
			}
			if ($player['captain_vote'] < 12) {
				$cap_votes[$player['captain_vote']] += 1;
				
				++$num_captain_votes;
			}
		}
		
		foreach ($map_votes as $k => $v) {
			$this->data['maps'][$k]['votes'] = $v;
		}
		foreach ($cap_votes as $k => $v) {
			$this->data['players'][$k]['votes'] = $v;
		}
		
		// check if all players have voted for captains
		if ($num_captain_votes > 11) {
			
			$this->data['gather']['status'] = 'play';
		}
		
		return true;
	}
	
	private function validate($ip, $port, $password) {
		// IP
		$ip = preg_replace('/\s+/', '', $ip);
		$test = ip2long($ip);
		if ( $test == -1 || $test === false ) {
			throw new Exception("IP is invalid '{$ip}' in gather '{$id}'.");
		} else {
			$server = $ip;
		}
		
		// PORT
		$port = preg_replace('/\s+/', '', $port);
		if ( !is_numeric($port) ) {
			throw new Exception("Port is invalid '{$port}' in gather '{$id}'.");
		} else {
			$server .= ':'.$port;
		}
		
		// PASSWORD
		$password = preg_replace('/\s+/', '', $password);
		$server .= ' '.$password;
		
		return $server;
	}
	
	private function reindex($a) {
		// understand logic
		$new = array();

		foreach ($a as $k => $v) {
			if (is_array($v)) {
			   $n[$k] = array_values($v);
			}
		}
		
		return $n;
	}
	
	public function render($id) {
		$players = $this->data['players'];
		$maps = $this->data['maps'];
		
		$num_players = count($players);
		$num_players_missing = 12 - $num_players;
		$num_maps = count($maps);
		$status = $this->status();
		
		$c = 'var g = false;var r = \''.$status.'\';if ($.cookie(\'gather\') !== null) {g = true;}$(\'div#gameserver\').html(\'<a href="steam://run/4920//connect '.$this->getGameServer().'">steam://run/4920//connect '.$this->getGameServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#voiceserver\').html(\'<a href="'.$this->voiceType().'://'.$this->getVoiceServer().'">'.$this->voiceType().'://'.$this->getVoiceServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#status\').html(\'';
		
		if ( $status === 'wait' ) {
			$c = $c.'Waiting for '.$num_players_missing.' more.';
		} elseif ( $status === 'vote' ) {
			$c = $c.'Voting for captains.';
		} elseif ( $status === 'play' ) {
			$c = $c.'Game played.';
		}
		$c = $c.'\');$(\'span#playercount\').html(\''.$num_players.'\');$(\'ul#playerlist\').html(\'';
		foreach ($players as $player) {
			$c = $c.'<li><img src="'.$player['avatar'].'" alt="" /><a href="http://steamcommunity.com/profiles/'.$player['id'].'">'.$player['name'].'</a></li>';
		}
		$c = $c.'\');if (g) {$(\'ul#maplist\').html(\'';
		foreach ($maps as $map) {
			$c = $c.'<li><a href="vote.php?id='.key($maps).'&type=map">'.$map['name'].'</a> ('.$map['votes'].')</li>';
		}
		$c = $c.'\');} else {$(\'ul#maplist\').html(\'';
		foreach ($maps as $map) {
			$c = $c.'<li>'.$map['name'].'</a> ('.$map['votes'].')</li>';
		}
		$c = $c.'\');}';
		
		$f = './gathers/'.$id.'.js';
		$fh = fopen($f, 'w') or die('ERROR: cannot open/create js');
		fwrite($fh, $c);
		fclose($fh);
	}
	
	private function regather() {
		// get current gather number
		$f_0 = file_get_contents('global.txt') or die('ERROR: cannot open global.txt');
		$gather = $f_0;
		
		// increase gather number
		++$gather;
		
		// create new gather json
		$f_1 = './gathers/'.$gather.'.json';
		$fh_1 = fopen($f_1, 'w') or die('ERROR: cannot create new gather json');
		
		$c_1 = '{"gather":{"id":'.$gather.',"game_server":"255.255.255.255:27015 pug","voice_server":"255.255.255.255:27016 voice","voice_type":"mumble","status":"wait"},"players":[],"maps":[{"name":"ns2_summit","votes":0},{"name":"ns2_veil","votes":0},{"name":"ns2_tram","votes":0},{"name":"ns2_refinery","votes":0},{"name":"ns2_mineshaft","votes":0},{"name":"ns2_docking","votes":0}]}';
		fwrite($fh_1, $c_1);
		
		fclose($fh_1);
		
		// create new chat textfile
		$f_2 = './gathers/'.$gather.'.txt';
		$fh_2 = fopen($f_2, 'w') or die('ERROR: cannot create new chat textfile');
		
		$c_2 = 'Welcome to Gather #'.$gather.'.<br />';
		fwrite($fh_2, $c_2);
		
		fclose($fh_2);
		
		compile($gather);

		// update global.txt
		$f_3 = './global.txt';
		$fh_3 = fopen($f_3, 'w') or die('ERROR: cannot open global.txt to update index');
		fwrite($fh_3, (string)$gather);
		fclose($fh_3);

		// chmod
		chmod($f_1, 0766);
		chmod($f_2, 0766);
		chmod($f_3, 0766);
	}
}
?>