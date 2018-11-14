<?php
/**
 * Health Check plugin for Craft CMS
 *
 * Adds a health check route to your site to indicate that Craft is health
 * and ready to accept web traffic from a load balancer.
 *
 * @author    Benjamin Smith
 * @copyright Copyright (c) 2016 Benjamin Smith
 * @link      https://www.benjaminsmith.com/
 * @package   HealthCheck
 * @since     1.0.0
 */

namespace benjaminsmith\craft_healthcheck;

use Craft;
use craft\base\Plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;

class HealthCheckPlugin extends Plugin
{

    public function init()
    {
        parent::init();

        Event::on(
            UrlManager::class,
                UrlManager::EVENT_REGISTER_SITE_URL_RULES,
                function(RegisterUrlRulesEvent $event) {
                    $route = $this->getSettings()->url;
                    $event->rules[$route] = 'healthcheck/health-check/render-health-check';
                }
        );
    }

    public function getName()
    {
         return Craft::t('Health Check');
    }

    public function getDescription()
    {
        return Craft::t('Adds a health check route to your site to indicate that Craft is health and ready to accept web traffic from a load balancer.');
    }

    public function getDocumentationUrl()
    {
        return 'https://github.com/benjamin-smith/craft-healthcheck/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/benjamin-smith/craft-healthcheck/master/releases.json';
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper()
    {
        return 'Benjamin Smith';
    }

    public function getDeveloperUrl()
    {
        return 'https://www.benjaminsmith.com/';
    }

    public function hasCpSection()
    {
        return false;
    }

    protected function createSettingsModel()
    {
        return new \benjaminsmith\craft_healthcheck\models\Settings();
    }
}

