<?php

namespace Codemax\CentovaCast;

use http\Exception;

trait CCFunctions
{
    /**
     * Retorna todos os servidores disponíveis
     * @return mixed
     */
    public function listServers() //-- WORK only root
    {
        return $this->runQuery('system.listhosts');
    }

    /**
     * Lista todas as contas no servidor
     * @return mixed
     */
    public function listAccounts() //-- WORK
    {
        return $this->runQuery('system.listaccounts');
    }

    /**
     * Criar uma Nova Conta
     *
     * @param $args
     */
    public function createAccount($args)
    {
        try{
            if (empty($args['ouvintes']) || !isset($args['ouvintes'])) {
                $this->reportError('ouvintes', 'Quantidade de Ouvintes não definido.');
            }

            if (empty($args['bitrate']) || !isset($args['bitrate'])) {
                $this->reportError('bitrate', 'Qualidade Bitrate não definido.');
            }

            if (empty($args['disco']) || !isset($args['disco'])) {
                $this->reportError('disco', 'Espaço em Disco não definido.');
            }

            if (empty($args['senha']) || !isset($args['senha'])) {
                $this->reportError('senha', 'Senha não definida.');
            }

            if (empty($args['usuario']) || !isset($args['usuario'])) {
                $this->reportError('usuario', 'Usuário não definido.');
            }

            if (empty($args['email']) || !isset($args['email'])) {
                $this->reportError('email', 'E-mail Usuário não definido.');
            }

            if (empty($args['trafego']) || !isset($args['trafego'])) {
                $this->reportError('trafego', 'Limite de Tráfego não definido.');
            }

            $array = array(
                'hostname' => 'auto',
                'ipaddress' => 'auto',
                'port' => 'auto',
                'username' => @$args['usuario'],
                'adminpassword' => @$args['senha'],
                'sourcepassword' => @$args['senha'].'dj',
                'email' => @$args['email'],
                'title' => @$args['nome'],
                'organization' => @$args['empresa'],
                'maxclients' => @$args['ouvintes'],
                'maxbitrate' => @$args['bitrate'],
                'transferlimit' => @$args['trafego'],
                'diskquota' => @$args['disco'],
                'servertype' => @$args['tipo'],
                'introfile' => '',
                'fallbackfile' => '',
                'autorebuildlist' => 1,
            );

            $this->runQuery('system.provision', $array, true);

        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * Edita uma Conta
     *
     * @param $username
     * @param $args
     * @return mixed
     */
    public function editAccount($username, $args)
    {
        $array = array(
            'hostname' => 'auto',
            'ipaddress' => 'auto',
            'port' => 'auto',
            'adminpassword' => @$args['senha'],
            'sourcepassword' => @$args['senha'].'dj',
            'email' => @$args['email'],
            'title' => @$args['nome'],
            'organization' => @$args['empresa'],
            'maxclients' => @$args['ouvintes'],
            'maxbitrate' => @$args['bitrate'],
            'transferlimit' => @$args['trafego'],
            'diskquota' => @$args['disco'],
            'servertype' => @$args['tipo'],
        );
        return $this->runQuery('server.reconfigure', array_merge(['username' => $username], $array));
    }

    /**
     * Suspende uma Conta
     *
     * @param $username
     * @return mixed
     */
    public function suspendAccount($username)
    {
        return $this->runQuery('system.setstatus', ['username' => $username, 'status' => 'disabled']);
    }

    /**
     * Reativa uma Conta
     *
     * @param $username
     * @return mixed
     */
    public function unsuspendAccount($username)
    {
        return $this->runQuery('system.setstatus', ['username' => $username, 'status' => 'enabled']);
    }

    /**
     * Remove uma Conta Permanentemente
     *
     * @param $username
     * @return mixed
     */
    public function removeAccount($username)
    {
        return $this->runQuery('system.terminate', ['username' => $username, 'clientaction' => 'delete']);
    }

    /**
     * Mostra detalhes de uma conta específica
     *
     * @param $username
     * @return mixed
     */
    public function getAccount($username)
    {
        return $this->runQuery('server.getaccount', ['username' => $username]);
    }

    /**
     * Inicia o serviço Streaming
     *
     * @param $username
     * @return mixed
     */
    public function startStream($username)
    {
        return $this->runQuery('server.start', ['username' => $username]);
    }

    /**
     * Reinicia o Serviço Streaming
     *
     * @param $username
     * @return mixed
     */
    public function restartStream($username)
    {
        return $this->runQuery('server.restart', ['username' => $username]);
    }

    /**
     * Para o Serviço Streaming
     *
     * @param $username
     * @return mixed
     */
    public function stopStream($username)
    {
        return $this->runQuery('server.stop', ['username' => $username]);
    }

    /**
     * Exibe o Streaming ativo
     *
     * @param $username
     * @param $mountpoints
     * @return mixed
     */
    public function getStream($username, $mountpoints)
    {
        return $this->runQuery('server.getstatus', ['username' => $username, 'mountpoints' => $mountpoints]);
    }

    /**
     * Lista todos os usuários no servidor
     *
     * @return array
     */
    public function listUsernames()
    {
        try {
            @$accounts = $this->runQuery('system.listaccounts')->response->data;
        } catch (Exception $e) {
            // The system don't have any account yet
            return [];
        }
        $usernames = [];
        if($accounts != NULL)
        foreach ($accounts as $value) {
            $usernames[] = $value->username;
        }
        return $usernames;
    }
}