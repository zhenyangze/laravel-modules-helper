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
        $dataType = request()->get('format_type');
        if (!empty($callback)) {
            return response(($callback . "('" . json_encode($this->formatData($jsonData, $dataType)) . "')"));
        }
        
        return response()->json($this->formatData($jsonData, $dataType));
    }

    public function formatData($arrData = [], $dataType = '')
    {
        if ($dataType == 'flatten') {
            $arrData = $this->flatten($arrData);
        }

        return $arrData;
    }

    /**
     * appendSourceKey 
     *
     * @param $item
     * @param $key
     *
     * @return 
     */
    function appendSourceKey($item, $key = '')
    {
        if (empty($key)) {
            return $item;
        }

        $data = [];
        $data[array_get(config('modules_helper.response_format'), 'key', 'source_key')] = $key;
        $data[array_get(config('modules_helper.response_format'), 'data', 'source_data')] = $item;

        return $data;

    }
    function flatten($item, $topKey = '') {
        // 特殊数值
        if (!is_array($item) || empty($item) || count($item) == 0) {
            return $this->appendSourceKey($item, $topKey);
        }

        $keys = array_keys($item);
        // 索引正好等于当前数组的key
        $newArr = [];
        if ($keys == range(0, count($item) - 1) || !is_numeric(implode('', $keys))) {
            foreach($item as $key => $value) {
                //这里需要根据情况判断value是否要处理
                $newItem = $this->flatten($value);
                $newArr[$key] = $newItem;
            }
        } else {
            //非数值型的Key
            foreach($item as $key => $value) {
                //这里需要根据情况判断value是否要处理
                $newItem = $this->flatten($value, $key);
                $newArr[] = $newItem;
            }
        }
        return $this->appendSourceKey($newArr, $topKey);
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
