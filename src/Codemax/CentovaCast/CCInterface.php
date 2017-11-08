<?php

namespace Codemax\CentovaCast;


interface CCInterface
{
    public function setHost($host);
    public function setPassword($pass);
    public function setUsername($user);
    public function setPort($port);
    public function getHost();
    public function getPassword();
    public function getUsername();
    public function getPort();
}