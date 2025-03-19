<?php

namespace Auth\Enums;

enum AuthEnum: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
}