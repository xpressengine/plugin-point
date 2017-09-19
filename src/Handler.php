<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugins\Point;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Xpressengine\Config\ConfigManager;
use Xpressengine\Plugins\Point\Models\Log;
use Xpressengine\Plugins\Point\Models\Point;
use Xpressengine\User\UserInterface;

/**
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class Handler
{
    use DispatchesJobs;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var ConfigManager
     */
    private $config;

    protected $actions = null;

    /**
     * Handler constructor.
     *
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin, ConfigManager $config)
    {
        $this->plugin = $plugin;
        $this->config = $config;
    }

    public function storeActionInfo($action, $info)
    {
        $this->config->set('point.'.$action, $info);
    }

    public function storeActionPoint($action, $point = null)
    {
        if ($point !== null) {
            $action = [$action => $point];
        }

        $action = array_dot($action);

        foreach ($action as $name => $point) {
            $this->config->setVal('point.'.$name.'.point', $point);
        }
    }

    public function getActionTitle($action, $default = '')
    {
        $config = $this->config->get('point.'.$action, true);
        return $config->get('title', $default);
    }

    public function getActionPoint($action, $default = null)
    {
        $config = $this->config->get('point.'.$action, true);
        return $config->get('point', $default);
    }

    public function getAllActions()
    {
        if ($this->actions !== null) {
            return $this->actions;
        }
        $this->actions = [];
        $root = $this->config->get('point');
        $this->traverseConfig($root);
        return $this->actions;
    }

    protected function traverseConfig($action)
    {
        $this->actions[$action->name] = $action;
        foreach ($this->config->children($action) as $child) {
            $this->traverseConfig($child);
        }
    }

    public function executeAction($action, $user, $content = [])
    {
        $config = $this->config->get('point.'.$action, true);
        $score = $config->get('point');

        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        $this->logging($action, $user, $score, $content);

        $point = $this->getPointObj($user);
        $point->point = $point->point + $score;
        $point->save();
    }

    protected function logging($action, $userId, $point, $content = [])
    {
        $log = new Log();
        $log->user_id = $userId;
        $log->action = $action;
        $log->content = $content;
        $log->point = $point;
        $log->save();
    }

    public function setUserPoint($user, $point, $content = [])
    {
        $this->logging('init', $user->getId(), $point, $content);

        $pointObj = $this->getPointObj($user->getId());
        $pointObj->point = $point;
        $pointObj->save();
    }

    public function addUserPoint($user, $point, $content = [])
    {
        $this->logging('add', $user->getId(), $point, $content);

        $pointObj = $this->getPointObj($user->getId());
        $pointObj->point = $pointObj->point + $point;
        $pointObj->save();
    }

    public function getPoint($user)
    {
        if ($user instanceof UserInterface) {
            $userId = $user->getId();
        } else {
            $userId = $user;
        }

        $pointObj = $this->getPointObj($userId);

        return $pointObj->point;
    }

    protected function getPointObj($userId)
    {
        $pointObj = Point::find($userId);
        if ($pointObj === null) {
            $pointObj = new Point(['user_id' => $userId, 'point' => 0]);
        }
        return $pointObj;
    }

}
