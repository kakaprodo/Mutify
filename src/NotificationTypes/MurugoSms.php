<?php
namespace RWBuild\Mutify\NotificationTypes;

trait MurugoSms
{

    /**
     * MurugoSms
     * ------------------------------------------------------------------------
     * This handles sms notification, the trait will validate and build
     * data to be sent to murugo sms notification
     */

    protected $phoneNumber;

    protected $message;

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;

    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    private function getSmsValidatedData()
    {
        $data = $this->buildSmsRequestData();

        $this->validateSmsRequestData($data);

        return $data;
    }

    /**
     * build request data for sms notification
     */
   private function buildSmsRequestData()
    {
        return  [
            'numbers' => $this->phoneNumber,
            'message' => $this->removeDuplicateWords($this->message)
        ];
    }

    /**
     * Validate only data that will be send for channel notification 
     */
    private function validateSmsRequestData(array $data)
    {
        $this->keyExist(
            ['numbers','message'], $data
        );
    }
}