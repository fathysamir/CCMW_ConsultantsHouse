<?php

namespace App\Console\Commands;

use App\WebSockets\Chat;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
// use MyApp\Chat;
use React\EventLoop\Loop;

class WebSocketServer extends Command
{
    protected $signature = 'websockets:init';

    protected $description = 'Start the WebSocket server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $loop = Loop::get();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat($loop)
                )
            ),
            8080
        );

        $this->info('WebSocket server started on port 8080');
        $server->run();
    }
}
