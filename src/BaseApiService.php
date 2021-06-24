<?php
namespace StrongNguyen29\ApiService;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseApiService implements BaseApiInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * BaseService constructor.
     */
    public function __construct()
    {
        $this->config = config($this->getConfigName());
    }

    /**
     * get Array register api endpoint
     *
     * @return string
     */
    abstract function getConfigName() : string;

    /**
     * Default query params for all api
     *
     * @return null
     */
    public function getQueryParamDefault() {
        return null;
    }

    /**
     * get Auth token
     *
     * @return null
     */
    public function getToken() {
        return $this->config['api_token'];
    }

    /**
     * Http call request
     *
     * @param string $action
     * @param array $params
     * @param string $pathData
     * @return array|null
     */
    public function execApi($action, $params, $pathData = '') {
        try {
            if (!isset($this->config['endpoints'][$action]['path'])) {
                $this->log(self::class . '@execApi: ' . $action . ' | Error: endpoint not defined');
                return null;
            }

            // get url
            $pathUrl = $this->config['endpoints'][$action]['path'];
            $url = sprintf('%s/%s/%s', $this->config['main_url'], $pathUrl, $pathData);
            $url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);

            if ($defaultParams = $this->getQueryParamDefault()) {
                $url .= '?' . Arr::query($defaultParams);
            }

            // GEt method, token api
            $method = $this->config['endpoints'][$action]['method'] ?? 'get';
            $token = $this->getToken();

            //Bat dau tinh thoi gian goi api
            $execTimes = -hrtime(true);
            // Call api
            $http = Http::withToken($token);
            switch ($method) {
                default:
                    $response = $http->get($url, $params);
                    break;
                case 'post':
                    $response = $http->post($url, $params);
                    break;
                case 'put':
                    $response = $http->put($url, $params);
                    break;
                case 'delete':
                    $response = $http->delete($url, $params);
                    break;
            }
            // End tinh time call, quy doi ra mili giay
            $execTimes += hrtime(true);
            $execTimes = $execTimes / 1e+6;

            $this->log(self::class . '@execApi: ' . $action, [
                'exec_time' => $execTimes,
                'token' => $token,
                'request' => ['url' => $url, 'params' => $params],
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    return isset($data['data']) ? $data['data'] : true;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error(self::class . '@execApi error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|null
     */
    public function __call($name, $arguments)
    {
        $this->log(self::class . '@__call', ['$name' => $name, '$arguments' => $arguments]);
        return $this->execApi($name, $arguments[0]['params'] ?? [], $arguments[0]['path'] ?? '');
    }

    /**
     * @param $mes
     * @param $context
     * @param string $type
     */
    protected function log($mes, $context = [], $type = 'debug') {
        if (!isset($this->config['logged']) || !$this->config['logged']) {
            return;
        }

        if (!is_array($context)) $context = [$context];
        switch ($type) {
            case 'error':
                Log::error($mes, $context);
                break;
            case 'info':
                Log::info($mes, $context);
                break;
            case 'debug':
                Log::debug($mes, $context);
                break;
        }
    }
}