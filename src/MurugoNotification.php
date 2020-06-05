<?php
namespace RWBuild\Mutify;

use Carbon\Carbon;
use GuzzleHttp\Client;
use RWBuild\Mutify\NotificationTypes\MurugoSms;
use RWBuild\Mutify\NotificationTypes\MurugoEmail;
use RWBuild\Mutify\NotificationTypes\MurugoPusher;
use RWBuild\Mutify\Jobs\SendMurugoNoficiationAfter;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class MurugoNotification
{
    /**
     * MurugoNotification
     * ------------------------------------------------------------------------
     * A service class to handle all morugo notification, the class is using 
     * traits to handle different type of notification, all setter methods
     * are defined in their corresponding traits depending on notification
     * type. this service support also queueable murugo notificatioon
     */

    use MurugoEmail, MurugoPusher, MurugoSms;

    /**
     * Type of notification that murugo should broadcast
     * 
     * @var String
     */
    public $type = null;

    /**
     * guzzle client
     */
    protected $httpClient;

    /**
     * will decide if a notification will be executed in sync or async mode
     */
    protected $isQueuable = false;

    /**
     * data to be sent with the request
     */
    protected $requestData;

    /**
     * the queue on which a notification should run on
     */
    protected $onQueue = 'default';

    /**
     * base url for murugo notification
     */
    protected $baseUrl = 'https://test.notificationservice.murugo.cloud';

    /**
     * Periode of time in second af
     */
    protected $sendAt = 0;

    /**
     * define weither a request was successfully done
     */
    protected $requesStatus = false;

    /**
     * message of the sent request
     */
    protected $message = null;

    /**
     * define weither a package can throw error on failed request notification
     */
    protected $throwable = false;

    /**
     * The response for guzzle request
     */
    protected $response = null;

    /**
     * murugo notification types
     */
    const EMAIL = 'email';
    const PUSHER = 'pusher';
    const SMS = 'sms';

    /**
     * define weither notification message has to be displayed only for test
     */
    protected $forTest = false;

    /**
     * Set the notification type
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * define that a pusher notification has to be sent
     */
    public static function pusher()
    {
        return (new MurugoNotification())
                    ->setType(self::PUSHER);
    }

    /**
     * define that an sms notification has to be sent
     */
    public static function sms()
    {
        return (new MurugoNotification())
                    ->setType(self::SMS);
    }

    /**
     * define that an email notification has to be sent
     */
    public static function email()
    {
        return (new MurugoNotification())
                    ->setType(self::EMAIL);
    }

    /**
     * define that no notification will be sent, only data will be dumped
     */
    public function test($forTest = true)
    {
        $this->forTest = $forTest;
        return $this;
    }

    /**
     * define if notification can be queued or not
     */
    public function queuable($isQueuable = true)
    {
        $this->isQueuable = $isQueuable;
        return $this;
    }

    /**
     * define weither a package can throw error on failed request notification
     */
    public function throwOnError($throwable = true)
    {
        $this->throwable = $throwable;

        return $this;
    }

    /**
     * define the queue on which the queued notification will be runed on
     */
    public function onQueue($queueName)
    {
        if ($queueName) $this->isQueuable = true;

        $this->onQueue = $queueName ?? 'default';

        return $this;
    }

    /**
     * define time when a queued notification has to be executed
     */
    public function sendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * check weither the request has been successfully done
     */
    public function isOk()
    {
        return $this->requesStatus;
    }

    /**
     * get the response of the guzzle request
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * return the message of request depending on request status
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Send the notification based on the notification type
     */
    public function send()
    {

        $this->requestData = $this->validateRequestData();

        $url = $this->baseUrl . $this->getApiEndpoint();

        if ($this->forTest) return dump($url, $this->requestData);

        if ($this->isQueuable) return $this->sendInQueue($url, $this->requestData);

        return $this->sendDirectly($url, $this->requestData);
        
    }

    /**
     * send a notification direclty without puting it in queue
     */
    private function sendDirectly($url, $requestData)
    {
        $this->response = $this->caller('sendRequest', $url, $requestData);

        return $this;
    }

    /**
     * send a request to notifocation server 
     */
    private function sendRequest($url, $requestData)
    {
        $response =  $this->httpClient->request('POST', $url, [
            'form_params' => $requestData
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * put notification to queue before sending it
     */
    private function sendInQueue($url, $requestData)
    {
        SendMurugoNoficiationAfter::dispatch($url, $requestData)
            ->delay(Carbon::now()->addSeconds($this->sendAt))
            ->onQueue($this->onQueue);

        return $this;
    }

    /**
     * validate an return validated data to send in request body
     */
    private function validateRequestData()
    {
        if ($this->type == self::PUSHER) return $this->getPusherValidatedData();

        if ($this->type == self::SMS) return $this->getSmsValidatedData();

        if ($this->type == self::EMAIL) return $this->getEmailValidatedData();
    }

    /**
     * define the endpoint according to the type of channel
     */
    private function getApiEndpoint()
    {
        $baseUrl = '/api/notifications';

        if ($this->type == self::PUSHER) return $baseUrl . '/pusher/';

        if ($this->type == self::SMS) return $baseUrl . '/sms/';

        if ($this->type == self::EMAIL) return $baseUrl . '/emails/';
    }

    /*
     * Helper to check if a specific key exits in an array
     */
    private function keyExist(Array $keys, Array $data)
    {
        foreach($keys as $key) {

            if (! isset($data[$key]) ) {

                throw new \Exception('Trying to send murugo ' .$this->type.
                                    ' notification without notification ' . $key, 500);
                                    
            }

        }
        
    }

    /**
     * remove duplicated consecutive words in a string
     */
    private function removeDuplicateWords ($myString) 
    {
        if (! $myString) return null;

        return preg_replace('/\b(\S+)(?:\s+\1\b)+/i', '$1', $myString);
    }


    /**
     * a helper to execute a given method that send guzzle request and throw error in case it occurs
     */
    private function caller($methodName, $methodParam1 = null, $methodParam2 = null, $methodParam3 = null)
    {
        try { 
            $resp = $this->$methodName(
                $methodParam1, $methodParam2
            );

            $this->requesStatus = true;

            $this->message = 'Successfully done';

            return $resp;

        } catch (ClientException $exception) {
            $this->catchError($exception);
        } catch (ConnectException $exception) {
            $this->catchError($exception);
        } catch (Exception $e) {
            fireErr($e->getMessage());
        }
    }

    /**
     * this will return an error message when something went wrong with guzzle client
     */
    private function catchError($exception)
    {  
        $this->requesStatus = false;
       
        if ($this->throwable) return $this->throwErr($exception);

        $response = $exception->getResponse();

        if (! $response) return $this->message = $exception->getMessage();
       
        $statusCode = $response->getStatusCode();
        $errorResp = json_decode($response->getBody());

        $this->message = $errorResp->message ?? $errorResp->error ?? $statusCode;

    }

    /**
     * render an execption
     */
    private function throwErr($exception)
    {
        $response = $exception->getResponse();
       
        if (! $response) return mutifyErr($exception->getMessage());

        $statusCode = $response->getStatusCode();
        $errorResp = json_decode($response->getBody());

        mutifyErrWith($errorResp->message ?? $errorResp->error ?? $statusCode)
            ->withStatus($statusCode)
            ->withData([
                'hint' => $exception->getMessage()
            ]);
    }


}