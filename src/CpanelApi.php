<?php
namespace Dezento;
/**
 *
 */
class CpanelApi
{
    private static
        $_instance = null,
        $_hasQueryParams,
        $_hasModule,
        $_hasMethod,
        $cpanelCredentials,
        $module,
        $method,
        $queryParams;

    public static function setCredentials(string $cpanelUrl, string $cpanelUser, string $cpanelPwd, string $cpanelPort='2083'): CpanelApi
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        self::$cpanelCredentials = new \stdClass();
        self::$cpanelCredentials->url = $cpanelUrl;
        self::$cpanelCredentials->user = $cpanelUser;
        self::$cpanelCredentials->password = $cpanelPwd;
        self::$cpanelCredentials->port = $cpanelPort;

        return self::$_instance;
    }

    private function getCredentials(): \stdClass
    {
        return self::$cpanelCredentials;
    }

    public function __call(string $name, array $arguments = null): CpanelApi
    {

        if (str_contains($name, 'Module')){
            self::$_hasModule = true;
            self::$module = str_replace('Module', '', $name . '/');

            return $this;
        }

        self::$_hasMethod = true;
        self::$method = $name;

        return $this;
    }

    private function getBasePath(): string
    {
        return "https://{$this->getCredentials()->url}:{$this->getCredentials()->port}/execute/";
    }

    public function setQueryParams(array $params): CpanelApi
    {
        if(!$params) {
            return throw new \Exception("setQueryParams method is empty.", 400);
        }

        self::$_hasQueryParams = true;

        self::$queryParams = '?' . http_build_query($params);

        return $this;
    }

    public function get(): string
    {

        if (!self::$_hasModule) {
            return throw new \Exception("Module not set.", 400);
        }

        if (!self::$_hasMethod) {
            return throw new \Exception("Method not set.", 400);
        }

        $queryString = $this->getBasePath() . self::$module . self::$method;

        if (self::$_hasQueryParams) {
            $queryString = $queryString  . self::$queryParams;
        }

        return $this->sendRequest($queryString);
    }


    private function sendRequest(string $queryString): string
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl, CURLOPT_HEADER,0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $header[0] = "Authorization: Basic " . base64_encode($this->getCredentials()->user.":".$this->getCredentials()->password) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $queryString);

        $result = curl_exec($curl);

        if ($result == false) {

            return throw new \Exception("Error: \"" . curl_error($curl) . "\" for $queryString", 400);
        }

        curl_close($curl);

        return $result;
    }
}
