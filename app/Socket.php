<?php

namespace MyApp;

use PDO;
use Exception;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;


class Socket implements MessageComponentInterface

{
    public function __construct($httpHost = '0.0.0.0', $port = 8080, $address = '0.0.0.0', LoopInterface $loop = null)
    {
        $this->clients = new \SplObjectStorage;
        $this->hospitals = [];
        $this->patients = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {

        // Store the new connection in $this->clients
        $this->clients->attach($conn);
        //echo "New connection! ({$conn})\n";
        $this->users[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        $data = json_decode($msg);
        echo $data->text7;
        switch ($data->text7) {
            case "Patient":
                $this->patients[$from->resourceId] = $data->text1;
                //echo $this->patients[$from->resourceId]."<br>";
            case "Hospital":
                $this->hospitals[$from->resourceId] = $data->text6;
                //echo $this->hospitals[$from->resourceId]."<br>";
        }
        foreach ($this->hospitals as $key => $value) {
            echo "value for id $key is $value";
        }
        if ($data->text7 == "Patient") {
            foreach ($this->hospitals as $key => $value) {
                if ($this->patients[$from->resourceId] == $value) {
                    $this->users[$key]->send($msg);
                }
            }
        }
        //$client->send("$msg");
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
