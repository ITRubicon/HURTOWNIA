<?php

namespace App\Entity;

interface IApiConnection
{
    public function getBaseUrl();
    public function getAuth();
    public function getAuthType();
    public function getName();
}