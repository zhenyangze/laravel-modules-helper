<?php
/**
 * Short description for ModulesUnauthorizedException.php
 *
 * @package Yangze\ModulesHelper\Exceptions
 * @author zhenyangze <zhenyangze@gmail.com>
 * @version 0.1
 * @copyright (C) 2019 zhenyangze <zhenyangze@gmail.com>
 * @license MIT
 */

namespace Yangze\ModulesHelper\Exceptions;

use Illuminate\Validation\UnauthorizedException;

class CommonUnauthorizedException extends UnauthorizedException implements CommonExceptions
{
}