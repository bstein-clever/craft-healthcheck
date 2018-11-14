<?php
namespace benjaminsmith\craft_healthcheck\controllers;

use benjaminsmith\craft_healthcheck\HealthCheckPlugin;

use Craft;
use craft\web\Controller;
use craft\web\View;
use yii\base\Exception;
use yii\web\HttpException;

class HealthCheckController extends Controller
{

    protected $allowAnonymous = array('render-health-check');

    /**
     * Render site status for a load balancer health check.
     *
     * This route does *not* determine if the site is "unhealthy". Craft
     * CMS is smart enough to send a 500 error if the site is offline,
     * including if the database is unavailable or some other dependency
     * is not met. This method simply ensures the request is authorized,
     * and returns a "success" response based on the plugin config.
     */
    public function actionRenderHealthCheck()
    {
        // only allow whitelisted IPs past this point
        $ipWhitelist = HealthCheckPlugin::getInstance()->getSettings()->ipWhitelist;
        if ($ipWhitelist!==false) {
            if (!is_array($ipWhitelist)) {
                throw new Exception('[Health Check] $ipWhitelist must be either false or an array.');
            }

            $userIp = \Craft::$app->request->userIP;
            if ($this->ipIsWhitelisted($ipWhitelist, $userIp)===false) {
                // 404 rather than 401 to not leak the existance of this route
                throw new HttpException(404);
            }
        }

        // we're healthy, output to browser
        $outputFormat = HealthCheckPlugin::getInstance()->getSettings()->successOutputFormat;
        switch ($outputFormat) {

            case 'json':
                // respond with json
                return $this->asJson(array('healthy' => true));

            case 'text':
                // set our local template path
                $templatePath = 'healthcheck/healthCheckText';

                // output status to template
		$oldMode = \Craft::$app->view->getTemplateMode();
		\Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
		$html = \Craft::$app->view->renderTemplate($templatePath, array('statusText' => 'true'));
		\Craft::$app->view->setTemplateMode($oldMode);
                return $html;

            default:
                // plugin is misconfigured
                throw new Exception('[Health Check] Only "text" or "json" are valid optins for $successOutputFormat');
        }
    }

    public function ipIsWhitelisted(array $ipWhitelist, $ip)
    {
        $ipWhitelist = array_map('trim', $ipWhitelist);
        return in_array($ip, $ipWhitelist);
    }
}
