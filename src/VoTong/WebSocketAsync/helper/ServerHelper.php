<?php
namespace VoTong\WebSocketAsync;

use App;
use Config;
use Redis;

class ServerHelper
{
    public static function getLocalIpAddress()
    {
        $privateIP = trim(preg_replace('/\s\s+/', ' ', shell_exec("/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'")));
        
        if (App::environment() !== 'local')
        {
            $publicIP = self::getByCURL('http://169.254.169.254/latest/meta-data/public-ipv4');
            
            if ($publicIP)
            {
                return $publicIP;
            }
        }
        
        return $privateIP;
    }
    
    public static function publishDataToRedisChannel($message, $channel = 'gdm')
    {
        return Redis::publish(Config::get('socket.channel'), json_encode([
            'event'             => 'publish',
            'data'              => $message,
            'server_ip_address' => self::getLocalIpAddress(),
        ]));
    }
    
    public static function getUserConnectId($resourceId)
    {
        return str_replace('.', '', self::getLocalIpAddress() . $resourceId);
    }
    
    public static function getPrivateChannel()
    {
        return Config::get('socket.channel') . '_' . self::getLocalIpAddress();
    }
    
    public static function getGdmClientKey()
    {
        return Config::get('socket.channel') . '_' . 'clients';
    }
    
    public static function getByCURL($url)
    {
        // create curl resource
        $ch = curl_init();
        
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        // return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // set timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        // $output contains the output string
        $output = curl_exec($ch);
        
        //
        $info = curl_getinfo($ch);
        
        // http_code 404
        if ($info['http_code'] != '200')
        {
            return false;
        }
        
        // close curl resource to free up system resources
        curl_close($ch);
        
        return $output;
    }
    
    public static function getAvaiablePrimaryRedisEndpoint()
    {
        // Default endpoint connection
        $connection = Config::get('database.redis.default');
        
        // Local environment cannot get info Elasticache, so return default read endpoint
//		if(App::environment('local')) {
        return $connection;
//		}
        
        // Init client
//		$client = ElastiCacheClient::factory(array(
//			'region'  => env('AWS_REGION', 'ap-southeast-2'),
//			'version' => 'latest'
//		));
//
//		// Get Redis replication group info
//		$replicationGroups = $client->describeReplicationGroups(array(
//			'ReplicationGroupId' => env('AWS_REDIS_REPLICATION_GROUP_ID', 'sv-nz-wb02-dev')
//		));
//
//		// Set avaiable read endpoint connection info
//		if(!empty($endpoint['ReplicationGroups'][0]['NodeGroups'][0]["NodeGroupMembers"])) {
//			foreach($endpoint['ReplicationGroups'][0]['NodeGroups'][0]["NodeGroupMembers"] as $endpoint) {
//				if(!empty($endpoint["ReadEndpoint"]) &&  $endpoint["ReadEndpoint"]["CurrentRole"] == "primary") {
//					$connection['host'] = $endpoint["ReadEndpoint"]["Address"];
//					$connection['port'] = $endpoint["ReadEndpoint"]["Port"];
//				}
//			}
//		}
//
//		return $connection;
    }
}