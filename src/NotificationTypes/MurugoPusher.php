<?php
namespace RWBuild\Mutify\NotificationTypes;

trait MurugoPusher
{
    /**
     * MurugoPusher
     * ------------------------------------------------------------------------
     * This handles pusher notification, the trait will validate and build
     * data to be sent to murugo pusher notification
     */

    /**
     * Channel name to which murugo will broadcast
     * 
     * @var String
     */
    protected $channelName = null;

    /**
     * type of channel in case of pusher notification
     */
    protected $channelType;

    /**
     * The data that murugo will broadcast
     * 
     * @var Array
     */
    protected $payload = null;

    protected $eventName;
    
    /**
     * Set the channel that the notification shall be broadcast on
     */
    public function setChannelName(String $channelName)
    {
        $this->channelName = $channelName;

        $this->channelType = PUBLIC_CHANNEL;

        return $this;
    }

    /**
     * Set the channel that the notification shall be broadcast on
     */
    public function setChannelType(String $channelType)
    {
        $this->channelType = $channelType;

        return $this;
    }

    /**
     * Set event name that client will listen to
     */
    public function setEventName(String $eventName)
    {
        $this->eventName = $eventName;
        return $this;
    }

    /**
     * Set the notification payload
     */
    public function setPayload(Array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * build and validate data to be sent in the request body
     */
    private function getPusherValidatedData()
    {
        $data = $this->buildPusherRequestData();

        $this->validatePusherRequestData($data);

        return $data;

    }

    /**
     * build request data for pusher notification
     */
    private function buildPusherRequestData()
    {
        if (isset($this->payload['message'])) {

            $this->payload['message'] = $this->removeDuplicateWords(
                $this->payload['message']
            );
        }
        
        return [
            'channel' => $this->channelName,
            'eventName' => $this->eventName,
            'type' => $this->channelType,
            'payload' => $this->payload  
        ];
    }

    /**
     * Validate only data that will be send for pusher notification 
     */
    private function validatePusherRequestData(array $data)
    {
        $this->keyExist(
            ['channel','payload', 'type', 'eventName'], $data
        );
    }
}
