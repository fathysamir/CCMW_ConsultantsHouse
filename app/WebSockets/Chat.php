<?php

namespace App\WebSockets;

use App\Models\User;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    protected $loop;

    private $clientUserIdMap;

    public function __construct($loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        $this->clientUserIdMap = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);

        $userId = $queryParams['user_id'] ?? null;

        $userToken = $queryParams['token'] ?? null;
        $user = User::where('id', $userId)->first();

        if (! $user) {
            echo "Invalid user. Connection refused.\n";
            $conn->close();

            return;
        } else {

            $tokenParts = explode('|', $userToken);
            $tokenId = $tokenParts[0];
            $tokenValue = $tokenParts[1];

            // // Find the token by ID and user
            $token = $user->tokens()->where('id', $tokenId)->first();
            // //dd($userToken,$tokenId,$tokenValue,$token->token);

            // dd(Crypt::encryptString($tokenValue),$token->token);
            if ($token && hash('sha256', $tokenValue) === $token->token) {
                // Token matches
                $this->clients->attach($conn, $userId);
                $this->clientUserIdMap[$userId] = $conn;
                $date_time = date('Y-m-d h:i:s a');
                // $conn->send(json_encode(['type' => 'ping']));
                $this->periodicPing($conn);
                // echo "New connection! ({$conn->resourceId})\n";
                echo "[ {$date_time} ],New connection! User ID: {$userId}, Connection ID: ({$conn->resourceId})\n";

            } else {
                // Token does not match
                echo 'Token does not match.';
                $conn->close();

                return;
            }
        }

    }

    private function periodicPing(ConnectionInterface $conn)
    {
        $timer = 30; // Send a ping every 60 seconds

        $this->loop->addPeriodicTimer($timer, function () use ($conn) {
            try {
                // Try sending a ping message, if connection is closed, it'll throw an error
                $conn->send(json_encode(['type' => 'ping'])); // Send a ping
                $date_time = date('Y-m-d h:i:s a');
                echo "[ {$date_time} ], Ping sent to Connection {$conn->resourceId}\n";
            } catch (\Exception $e) {
                // If there's an error sending the ping, the connection is probably closed
                $date_time = date('Y-m-d h:i:s a');
                echo "[ {$date_time} ], Connection {$conn->resourceId} has closed during ping\n";
            }
        });
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;

        // echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
        //     , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $data = json_decode($msg, true);

        if (array_key_exists('pong', $data)) {
            echo sprintf('sss');
        } else {
            $AuthUserID = $this->clients[$from];
            $requestData = json_encode($data['data'], JSON_UNESCAPED_UNICODE);

            $from->send(json_encode(['type' => 'pong']));
            $date_time = date('Y-m-d h:i:s a');
            echo sprintf('[ %s ], New pong has been sent'."\n", $date_time);

        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages

        $userId = $this->clients[$conn];
        unset($this->clientUserIdMap[$userId]);
        $this->clients->detach($conn);
        $date_time = date('Y-m-d h:i:s a');
        echo "[ {$date_time} ],Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function getClientByUserId($userId)
    {
        return $this->clientUserIdMap[$userId] ?? null; // Retrieve client in one step
    }
}
