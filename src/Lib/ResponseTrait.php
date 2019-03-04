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
        $callback = request()->get('callback');
        if (!empty($callback)) {
            return response(($callback . "('" . json_encode($jsonData) . "')"));
        }
        
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
    public function sendSuccess($data = [], $message = 'success', $code = 200)
    {
        return $this->sendResponse($data, $message, $this->getCode($code, 'success', 200));
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
    public function sendError($message = 'error', $data = [], $code = 500)
    {
        return $this->sendResponse($data, $message, $this->getCode($code, 'error', 500));
    }

    /**
     * sendNotFound 
     *
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendNotFound($message = 'not found', $code = 404)
    {
        return $this->sendResponse([], $message, $this->getCode($code, 'not found', 404));
    }

    /**
     * sendNoAuth 
     *
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendNoAuth($message = 'no auth', $code = 401)
    {
        return $this->sendResponse([], $message, $this->getCode($code, 'unauthorized', 401));
    }

    /**
     * getCode 
     *
     * @param $code
     * @param $type
     * @param $default
     *
     * @return 
     */
    protected function getCode($code = null, $type = 'error', $default = 500)
    {
        if (empty($code)) {
            $code = array_get(config('modules_helper.http_code'), $type, $default);
        }

        return $code;
    }

    /**
     * sendFailValidate 
     *
     * @param $message
     * @param $code
     *
     * @return 
     */
    public function sendFailValidate($message = 'fail validate', $code = 422)
    {
        return $this->sendResponse([], $message, $this->getCode($code, 'fail validate', 422));
    }
}
