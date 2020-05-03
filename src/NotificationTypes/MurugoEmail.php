<?php
namespace RWBuild\Mutify\NotificationTypes;

trait MurugoEmail
{
    /**
     * MurugoEmail
     * ------------------------------------------------------------------------
     * This handles simple email notification, the trait will validate and build
     * data to be sent to murugo email notification
     */


    /**
     * email header
     */
    protected $header = null;

    /**
     * email subject
     */
    protected $subject;

    /**
     * name of the email receiver
     */
    protected $receiverName;

    /**
     * receiver email address
     */
    protected $emailAddress;

    /**
     * email content
     */
    protected $content;

    /**
     * email footer
     */
    protected $footer = null;
    
    public function setHeader($header)
    {
        $this->header = $header ?? null;

        return $this;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function setReceiverName(string $receiverName)
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    public function setFooter($footer)
    {
        $this->footer = $footer ?? null;

        return $this;
    }

    private function getDefaultHeader()
    {
        return env('APP_NAME');
    }

    private function getDefaultFooter()
    {
        $year = date('Y');

        return '@ ' . $year .' ' .$this->getDefaultHeader() . '. All rights reserved';
    }

    /**
     * build and validate data to be sent in the request body
     */
    private function getEmailValidatedData()
    {
        $data = $this->buildEmailRequestData();

        $this->validateEmailRequestData($data);

        return $data;

    }

    /**
     * build request data for Email notification
     */
    private function buildEmailRequestData()
    {

        $header = $this->header ?? $this->getDefaultHeader();

        $footer = $this->footer ?? $this->getDefaultFooter();
     
        return [
            'header' => $header,
            'subject' => $this->subject,
            'name' => $this->receiverName,
            'email' => $this->emailAddress,
            'message' => $this->removeDuplicateWords($this->content),
            'footer' => $footer
        ];
    }

    /**
     * Validate only data that will be send for Email notification 
     */
    private function validateEmailRequestData(array $data)
    {
        $this->keyExist(
            [
                'name',
                'email',
                'message',
                'subject',
                'header',
                'footer'
            ], $data
        );
    }
}