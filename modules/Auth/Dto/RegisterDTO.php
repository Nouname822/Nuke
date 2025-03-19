<?php

namespace Auth\Dto;

class RegisterDTO
{
    /**
     * @var string|null
     */
    public string|null $login = null;

    /**
     * @var string|null
     */
    public string|null $email = null;

    /**
     * @var string|null
     */
    public string|null $password = null;

    /**
     * @var string|null
     */
    public string|null $role = null;
}
