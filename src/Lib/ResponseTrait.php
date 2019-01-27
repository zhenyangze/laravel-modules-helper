<?php 
namespace Yangze\ModulesHelper\Lib;

/**
 * ResponseTrait 
 */
trait ResponseTrait{
    /**
     * sendResponse 
     *
     * @param $data
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendResponse($data = [], $message = '', $code = '')
    {
        $jsonData = [];
        $codeKey = array_get(config('modules_helper.http_key'), 'code', 'code');
        $messageKey = array_get(config('modules_helper.http_key'), 'message', 'message');
        $dataKey = array_get(config('modules_helper.http_key'), 'data', 'data');

        $jsonData[$codeKey] = $code;
        $jsonData[$messageKey] = $message;
        $jsonData[$dataKey] = $data;
        
        return response()->json($jsonData);
    }

    /**
     * sendSuccess 
     *
     * @param $data
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendSuccess($data = [], $message = 'success', $code = 2000)
    {
        return $this->sendResponse($data, $message, $code);
    }

    /**
     * sendError 
     *
     * @param $message
     * @param $data
     * @param $code
     *
     * @return 
     */
    public function sendError($message = 'error', $data = [], $code = 4000)
    {
        return $this->sendResponse($data, $message, $code);
    }

    /**
     * sendNotFound 
     *
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendNotFound($message = 'not found', $code = 4000)
    {
        return $this->sendResponse([], $message, $code);
    }

    /**
     * sendNoAuth 
     *
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendNoAuth($message = 'no auth', $code = 5000)
    {
        return $this->sendResponse([], $message, $code);
    }

}
