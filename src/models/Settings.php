<?php

namespace benjaminsmith\craft_healthcheck\models;

use craft\base\Model;

class Settings extends Model
{
    public $url = 'health-check';
    public $ipWhitelist = false;
    public $successOutputFormat = 'json';
}
