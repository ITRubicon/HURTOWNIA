<?php

namespace App\Entity;

interface IConnection
{
    public function getBaseUrl();
    public function getAuth();
    public function getAuthType();
    public function getName();
    public function hasFk(): bool;
}