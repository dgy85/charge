<?php
if (!defined('APPPATH')) exit('Access Denied');

class R_Controller extends CI_Controller
{
    // 当前请求类型
    protected $_method = '';
    // REST默认请求类型???
    protected $defaultMethod = 'GET';
    // REST允许的请求类型列表
    protected $allowMethod = array('GET', 'POST','OPTIONS');
    // 返回数据类型
    protected $_responseType = '';
    // 默认的资源类型
    protected $defaultType = 'json';
    // REST允许请求的资源类型列表
    protected $allowType = array('xml', 'json');
    // REST允许输出的资源类型列表
    protected $allowOutputType = array(
        'xml' => 'application/xml',
        'json' => 'application/json'
    );
    //允许的headers
    protected $allowHeaders = array(
        'Content-Type', 'Authorization', 'Accept', 'X-Requested-With', 'Token', 'Client-Language', 'API-Version'
    );
    //用户ID
    protected $_clientID = '';
    //用户密钥
    protected $_secret = '';

    public function __construct()
    {
        parent::__construct();

        //获取客户端请求的输出数据类型
        $responseType = $this->input->get_post('format', true);
        //设置返回类型
        $this->_responseType = in_array($responseType, array_keys($this->allowOutputType)) ? $responseType : $this->defaultType;
        $this->_method = $_SERVER["REQUEST_METHOD"];

        //是否允许的资源请求类型
        if (!in_array($this->_method, $this->allowMethod)) {
            $this->response(array('responseCode'=>METHOD_NOT_ALLOWED,'responseMsg'=>'不支持的资源类型'), 405);
        }

        //返回允许的资源类型
        if($this->_method === "OPTIONS") {
            header("Access-Control-Allow-Methods: ".implode(',',$this->allowMethod));
            header("Access-Control-Allow-Headers: ");
            header("Access-Control-Max-Age", 600);
            $this->response(array(),200);
        }

        //验证用户权限
        $headers = getallheaders();
        if(!isset($headers['Authorization'])){
            $this->response(array('responseCode'=>FORBIDDEN,'responseMsg'=>'Forbidden'),403);
        }
        $Authorization = explode(' ',$headers['Authorization']);
        if(sizeof($Authorization)!=2){
            $this->response(array('responseCode'=>FORBIDDEN,'responseMsg'=>'Forbidden'),403);
        }

        if(!$this->auth->verify($Authorization)){
            $this->response(array('responseCode'=>FORBIDDEN,'responseMsg'=>'Forbidden'),403);
        }
    }

    /**
     * get方法
     * @param $key
     * @return mixed
     */
    protected function get($key)
    {
        return $this->input->get($key,true);
    }

    /**
     *
     * @param $key
     * @return mixed
     */
    protected function post($key)
    {
        return $this->input->post($key,true);
    }

    /**
     * 编码数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @return string
     */
    protected function encodeData($data)
    {
        if (empty($data)) return '';
        if ('json' == $this->_responseType) {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = json_encode($data);
        } elseif ('xml' == $this->_responseType) {
            // 返回xml格式数据
            $data = xml_encode($data);
        } elseif ('php' == $this->_responseType) {
            $data = serialize($data);
        }// 默认直接输出
        $this->setContentType($this->_responseType);
        //header('Content-Length: ' . strlen($data));
        return $data;
    }

    /**
     * 获取当前请求的Accept头信息
     * @return string
     */
    protected function getAcceptType()
    {
        $type = array(
            'html' => 'text/html,application/xhtml+xml,*/*',
            'xml' => 'application/xml,text/xml,application/x-xml',
            'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
            'js' => 'text/javascript,application/javascript,application/x-javascript',
            'css' => 'text/css',
            'rss' => 'application/rss+xml',
            'yaml' => 'application/x-yaml,text/yaml',
            'atom' => 'application/atom+xml',
            'pdf' => 'application/pdf',
            'text' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv'
        );

        foreach ($type as $key => $val) {
            $array = explode(',', $val);
            foreach ($array as $k => $v) {
                if (stristr($_SERVER['HTTP_ACCEPT'], $v)) {
                    return $key;
                }
            }
        }
        return false;
    }

    /**
     * 输出返回数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @param integer $code HTTP状态
     * @return void
     */
    protected function response($data, $code = 200)
    {
        $this->sendHttpStatus($code);
        exit($this->encodeData($data));
    }

    /**
     * 设置页面输出的CONTENT_TYPE和编码
     * @access public
     * @param string $type content_type 类型对应的扩展名
     * @param string $charset 页面输出编码
     * @return void
     */
    public function setContentType($type, $charset = '')
    {
        if (headers_sent()) return;
        $type = strtolower($type);
        if (isset($this->allowOutputType[$type])) //过滤content_type
            header('Content-Type: ' . $this->allowOutputType[$type] . '; charset=' . $charset);
    }

    // 发送Http状态信息
    protected function sendHttpStatus($code)
    {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        }
    }
}