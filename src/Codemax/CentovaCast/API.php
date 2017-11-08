<?php

namespace Codemax\CentovaCast;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class API implements CCInterface
{
    use CCFunctions;

    private $format = 'json';
    private $host;
    private $port = 2199;
    private $use_ssl = false;
    private $username;
    private $password;
    public $response = array();
    public $error = array();
    public $success = array();

    /**
     * API constructor.
     * @param $host
     */
    public function __construct($options = array())
    {
        if(!$this->checkOptions($options))
        {
            $this->setHost($options['host']);
            $this->setUsername($options['username']);
            $this->setPassword($options['password']);
        }
    }

    public function getHost()
    {
        return $this->host;
    }

    public function reportError($param, $message)
    {
        $array = [
            'param' => $param,
            'verbose' => $message,
        ];

        array_push($this->error, $array);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPort(){
        return $this->port;
    }

    public function getUsername(){
        return $this->username;
    }

    public function responseJSON()
    {
        if(count($this->error) != 0)
        {
            $response = [
                'status' => 'error',
                'errors' => $this->error,
            ];

            echo \GuzzleHttp\json_encode($response);
        }else{
            $response = [
                'status' => 'success',
                'response' => $this->success
            ];

            echo \GuzzleHttp\json_encode($response);
        }
    }

    private function checkOptions($options)
    {
        if (empty($options['host'])) {
            $this->reportError('host', 'Servidor não configurado.');
        }
        if (empty($options['username'])) {
            $this->reportError('username', 'Usuário não definido.');
        }
        if (empty($options['password'])) {
            $this->reportError('password', 'Senha não definida.');
        }
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function setPassword($pass)
    {
        $this->password = $pass;
        return $this;
    }

    public function setUsername($user)
    {
        $this->username = $user;
        return $this;
    }

    protected function runQuery($action, $args = null, $throw=false)
    {
        $host = $this->getHost();
        $user = $this->getUsername();
        $pass = $this->getPassword();
        $port = $this->getPort();

        $protocol = ($this->use_ssl ? 'https' : 'http');

        $args['password'] = $user.'|'.$pass;

        $query = urldecode('&f='.$this->format .(!empty($args) ? '&'.http_build_query(['a' => $args]) : ''));
        //echo $action.$query;

        $client = new Client(['base_uri' => $protocol.'://'.$host.':'.$port]);

        try{
            $response = $client->request('GET', '/api.php?xm='. $action . $query);

            $return = (string) $response->getBody();
            /*
            list ($status, $dados, $msg) = explode ('|', $return);

            if ($status == 1){
                $success = [
                    'status' => $status,
                    'data' => $dados,
                    'verbose' => $msg
                ];

                array_push($this->success, $success);
            }else{
                $this->reportError('desconhecido', 'Permissão negada / Revenda não existe.');
            }

            $this->responseJSON();
            */

            echo $return;
        }
        catch(RequestException $e)
        {
            $erro = $e->getHandlerContext();
            $this->reportError('host', 'Não foi possível se conectar ao Servidor: '.$this->getHost());
            $this->responseJSON();
        }
    }
}