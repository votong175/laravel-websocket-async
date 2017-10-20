<?php
namespace VoTong\WebSocketAsync;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Handles all socket event and fires events within the system.
 *
 * @package  VoTong\WebSocketAsync
 */
class WebSocketAsyncEventListener implements MessageComponentInterface
{
    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    
    /**
     * Used event prefix.
     *
     * @var string
     */
    protected $_prefix;
    
    /**
     * Map from objects.
     *
     * @var \SplObjectStorage
     */
    protected $_clients;
    
    /**
     * Websocket Server's IP Address
     *
     * @var string
     */
    protected $ip_address;
    
    /**
     * Initial configuration.
     */
    public function __construct()
    {
        $this->_prefix    = WebSocketServiceProvider::SERVICE_PREFIX;
        $this->_clients   = new \SplObjectStorage;
        $this->ip_address = ServerHelper::getLocalIpAddress();
    }
    
    /**
     * Get singletom instance
     *
     */
    public final static function getInstance()
    {
        //Check instance
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        
        //Return instance
        return self::$_instance;
    }
    
    /**
     * Fire a event when a new connection has been opened.
     *
     * @param  ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $connection = new \VoTong\WebSocketAsync\WebSocketConnectionWrapper($conn);
        
        // Get user id from websocket url
        $queryString = $conn->WebSocket->request->getQuery()->toArray();
        
        // Get Connected User
        if (!empty($queryString['uid']))
        {
            $userId = (int) $queryString['uid'];
            $user   = User::where('user_id', $userId)->first();
            if (!empty($user))
            {
                $conn->user = $user;
            }
        }
        
        $event = Event::fire(
            "{$this->_prefix}.Listener.Open",
            [
                'connection' => $conn,
                'clients'    => $this->_clients,
                'listener'   => $this,
            ]
        );
        
        if ($event)
        {
            $this->_clients->attach($conn);
            
            Event::fire(
                "{$this->_prefix}.Listener.Open.After",
                [
                    'connection' => $conn,
                    'clients'    => $this->_clients,
                    'listener'   => $this,
                ]
            );
        }
    }
    
    /**
     * Fire a event when a message has been received through the tunnel.
     *
     * @param  ConnectionInterface $from
     * @param  string              $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $connection = new \VoTong\WebSocketAsync\WebSocketConnectionWrapper($from, $msg);
        
        $message = [
            'from'     => $from,
            'raw'      => $msg,
            'clients'  => $this->_clients,
            'listener' => $this,
        ];
//
        Event::fire("{$this->_prefix}.Listener.Message", $message);
    }
    
    /**
     * Process when Websocket Server publish data to Redis channel
     *
     * @param  ConnectionInterface $from
     * @param  string              $msg
     */
    public function onPublish($data)
    {
        $data = json_decode($data, true);

// 		if(isset($data['server_ip_address']) && $data['server_ip_address'] != $this->ip_address) {
// 			$data['data']['server_ip_address'] = $this->ip_address;

// 			var_dump($data);
// 			var_dump(count($this->_clients));
        
        $message = json_encode($data['data']);
        foreach ($this->_clients as $client)
        {
            $client->send($message);
        }
        
        echo "Publish done\n";
// 		}
    }
    
    /**
     * Fire a event when a connection has been closed.
     *
     * @param  ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $connection = new \VoTong\WebSocketAsync\WebSocketConnectionWrapper($conn);
        
        $event = Event::fire(
            "{$this->_prefix}.Listener.Close",
            [
                'connection' => $conn,
                'clients'    => $this->_clients,
                'listener'   => $this,
            ]
        );
        
        if ($event)
        {
            $this->_clients->detach($conn);
            echo "Connection {$conn->resourceId} has disconnected\n";
        }
        
        Event::fire(
            "{$this->_prefix}.Listener.Close.After",
            [
                'connection' => $conn,
                'clients'    => $this->_clients,
                'listener'   => $this,
            ]
        );
    }
    
    /**
     * Fire a event when a error has occurred.
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception          $exception
     */
    public function onError(ConnectionInterface $conn, \Exception $exception)
    {
        $connection = new \VoTong\WebSocketAsync\WebSocketConnectionWrapper($conn);
        
        Event::fire(
            "{$this->_prefix}.Listener.Error",
            [
                'connection' => $conn,
                'clients'    => $this->_clients,
                'listener'   => $this,
                'exception'  => $exception,
            ]
        );
        
        echo "An error has occurred: {$exception->getMessage()}\n";
        $conn->close();
    }
}
