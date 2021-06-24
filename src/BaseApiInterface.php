<?php


namespace StrongNguyen29\ApiService;


interface BaseApiInterface
{
    /**
     * get Array register api endpoint
     *
     * @return string
     */
    function getConfigName();

    /**
     * Set config
     */
    public function setConfig();

    /**
     * Default query params for all api
     *
     * @return null
     */
    public function getQueryParamDefault();

    /**
     * get Auth token
     *
     * @return null
     */
    public function getToken();

    /**
     * Http call request
     *
     * @param string $action
     * @param array $params
     * @param string $pathData
     * @return array|null
     */
    public function execApi($action, $params, $pathData = '');
}