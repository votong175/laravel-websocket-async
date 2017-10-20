<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Anton Samuelsson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
?>
<?php namespace VoTong\WebSocketAsync;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

/**
 * WebSocketAsync server class.
 *
 * @package  VoTong\WebSocketAsync
 */
class WebSocketServer
{
    /**
     * Ratchet WebSocketAsync server.
     *
     * @var Ratchet\Server\IoServer
     */
    protected $server;
    
    /**
     * Prepares a new WebSocketAsync server on a specified port.
     *
     * @param  integer $port
     *
     * @return VoTong\WebSocketAsync\WebSocketServer
     */
    public function start($port)
    {
        $this->server = IoServer::factory(new HttpServer(new WsServer(
            new \App\Providers\GdmWebSocketEventListener()
        )), $port);
        
        return $this;
    }
    
    /**
     * Starts the prepared server.
     *
     * @return VoTong\WebSocketAsync\WebSocketServer
     */
    public function run()
    {
        $this->server->run();
        
        return $this;
    }
}
