<?php namespace VoTong\WebSocketAsync;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider to instantiate the service.
 *
 * @package  VoTong\WebSocketAsync
 */
class WebSocketAsyncServiceProvider
    extends ServiceProvider
{
    
    /**
     * Internal service prefix.
     *
     * @var string
     */
    const SERVICE_PREFIX = 'Laravel.VoTong.WebSocketAsync';
    
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = false;
    
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
         //$this->package('freestream/websocket');
    }
    
    /**
     * Register the service provider.
     */
    public function register()
    {
        // async Interface
        $this->app->bind("VoTong\\WebSocketAsync\\AsyncInterface", function ()
        {
            return new Async(new \ZMQContext());
        });
        
        // WebSocketAsync Server
//        $this->app->bind("Ratchet\\MessageComponentInterface", "App\\Providers\\GdmWebSocketEventListener");
        
        $this->app['command.gdm_websocket:start'] = $this->app->share(function ($app)
        {
            return new WebSocketCommand(
                $this->app->make("VoTong\\WebSocketAsync\\AsyncInterface")
//				,$this->app->make("Ratchet\\MessageComponentInterface")
            );
        });
        
        $this->commands('command.websocket_async:start');
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['votong_websocket_async', 'command.websocket_async:start'];
    }
    
}
