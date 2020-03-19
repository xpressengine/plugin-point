<?php
/**
 * Handler.php
 *
 * PHP version 7
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
namespace Xpressengine\Plugins\Point;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Xpressengine\Config\ConfigManager;
use Xpressengine\Menu\Models\MenuItem;
use Xpressengine\Menu\ModuleHandler;
use Xpressengine\Plugins\Point\Models\Log;
use Xpressengine\Plugins\Point\Models\Point;
use Xpressengine\Routing\InstanceRoute;
use Xpressengine\User\UserInterface;

/**
 * Class Handler
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
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

    public function getConfig()
    {
        $config = $this->config->get('point');

        if ($config->get('function_use') != 'use') {
            $config->set('function_use', 'disuse');
        }
        if ($config->get('max_level') == null) {
            $config->set('max_level', 30);
        }
        if ($config->get('point_name') == null) {
            $config->set('point_name', 'point');
        }
        if ($config->get('level_icon') == null) {
            $config->set('level_icon', 'default');
        }
        if ($config->get('disable_download') != 'use') {
            $config->set('disable_download', 'disuse');
        }
        if ($config->get('disable_read_board') != 'use') {
            $config->set('disable_read_board', 'disuse');
        }
        return $config;
    }

    public function isUse()
    {
        $config = $this->getConfig();
        return $config->get('function_use') == 'use';
    }

    public function getLevelList()
    {
        $config = $this->getConfig();
        $maxLevel = $config->get('max_level');
        $list = [];
        for ($i = 1; $i <= $maxLevel; ++$i) {
            $list[] = $i;
        }

        return $list;
    }

    public function getIconOptions()
    {
        $default = [
            'default'
        ];

        return $default;
    }

    public function getIconPath()
    {
        $config = $this->getConfig();
        $path = '/plugins/point/assets/images/' . $config->get('level_icon');
        return $path;
    }

    public function getIcon($level)
    {
        return $this->getIconPath() . '/' . $level . '.gif';
    }

    public function getGroupConfig()
    {
        $config = $this->config->get('point.group');
        return $config;
    }

    public function getLevelPointConfig()
    {
        $config = $this->config->get('point.level_point');
        return $config;
    }

    public function getLevelByPoint($point)
    {
        $config = $this->getLevelPointConfig();
        $level = 0;
        $getLevelName = '';
        foreach($config->getPureAll() as $levelName => $checkPoint) {
            if ($checkPoint == '' || $checkPoint == 0) {
                continue;
            }

            if ($point > $checkPoint) {
                $getLevelName = $levelName;
            }

        }

        if ($getLevelName != '') {
            $parts = explode('_', $getLevelName);
            $level = array_pop($parts);
        }

        return $level;
    }

    public function getUserActions()
    {
        $actions = [
            'user_login' => [
                'default' => 0,
            ],
            'user_register' => [
                'default' => 0,
            ],
        ];

        return $actions;
    }

    public function getBoardActions()
    {
        $actions = [
            'board.write-document' => [
                'default' => 0
            ],
            'board.delete-document' => [
                'default' => 0
            ],
            'board.write-comment' => [
                'default' => 0
            ],
            'board.delete-comment' => [
                'default' => 0
            ],
            'board.upload-file' => [
                'default' => 0
            ],
            'board.download-file' => [
                'default' => 0
            ],
            'board.read-document' => [
                'default' => 0
            ],
            'board.receive-assent-document' => [
                'default' => 0
            ],
            'board.receive-dissent-document' => [
                'default' => 0
            ],
        ];

        return $actions;
    }

    public function getInstanceConfigName()
    {
        // name => config instance name
        $names = [
            'board' => 'module/board@board',
        ];
        return $names;
    }

    public function getInstanceList()
    {
        $names = $this->getInstanceConfigName();

        $instanceGroup = [];
        foreach ($names as $type => $instanceName) {
            $menuItems = MenuItem::where('type', short_module_id($instanceName))->get();
            $instances = [];
            foreach ($menuItems as $menuItem) {
                $instances[] = $menuItem;
            }
            $instanceGroup[$type] = $instances;
        }

        return $instanceGroup;
    }

    public function storeConfig(array $args)
    {
        $current = $this->getConfig();
        foreach ($args as $key => $value) {
            $current->set($key, $value);
        }
        $this->config->modify($current);
    }

    public function storeGroup(array $args)
    {
        $current = $this->getGroupConfig();
        foreach ($args as $key => $value) {
            $current->set($key, $value);
        }
        $this->config->modify($current);
    }

    public function storeLevelPoint(array $args)
    {
        $current = $this->getLevelPointConfig();
        foreach ($args as $key => $value) {
            $current->set($key, $value);
        }
        $this->config->modify($current);
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

    public function checkAction($action, $user)
    {
        if ($this->isUse() == false) {
            return true;
        }

        $score = $this->getActionPoint($action);
        if ($score >= 0) {
            return true;
        }

        $point = $this->getPoint($user);
        if ($point + $score >= 0) {
            return true;
        }


        return false;
    }

    public function executeAction($action, $user, $content = [])
    {
        if ($this->isUse()) {
            $score = $this->getActionPoint($action);

            if ($user instanceof UserInterface == false) {
                $user = app('xe.user')->find($user);
            }

            // guest skip
            if ($user->getId() != '' && $score != 0) {
                $this->addUserPoint($user, $score, $content, $action);
            }
        }
    }

    public function logging($action, $userId, $point, $content = [])
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
        $this->addUserPoint($user, $point, $content, 'init');
    }

    public function addUserPoint($user, $point, $content = [], $action = null)
    {
        if ($action == null) {
            $action = 'add';
        }
        $this->logging($action, $user->getId(), $point, $content);

        $pointObj = $this->getPointObj($user->getId());
        $currentLevel = $pointObj->level;
        $point = $pointObj->point + $point;
        $level = $this->getLevelByPoint($point);

        $pointObj->point = $point;
        $pointObj->level = $level;
        $pointObj->save();

        // check group
        if ($currentLevel < $level) {
            $this->levelUp($pointObj, $currentLevel, $level);
        } elseif ($currentLevel > $level) {
            $this->levelDown($pointObj, $currentLevel, $level);
        }
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

    public function getPointObj($userId)
    {
        $pointObj = Point::find($userId);
        if ($pointObj === null) {
            $pointObj = new Point(['user_id' => $userId, 'point' => 0]);
        }
        return $pointObj;
    }

    public function levelUp($pointObj, $from, $to)
    {
        $config = $this->getGroupConfig();
        foreach($config->getPureAll() as $groupId => $level) {
            if ($level == $to) {
                $this->addGroup($pointObj->user_id, $groupId);
                break;
            }
        }
    }

    public function levelDown($pointObj, $from, $to)
    {
        $config = $this->getGroupConfig();
        foreach($config->getPureAll() as $groupId => $level) {
            if ($level == $from) {
                $this->removeGroup($pointObj->user_id, $groupId);
                break;
            }
        }
    }

    public function addGroup($userId, $groupId)
    {
        $userHandler = app('xe.user');
        $user = $userHandler->users()->with('groups', 'emails', 'accounts')->find($userId);
        $groups = [];
        foreach($user->getGroups() as $group) {
            $groups[] = $group->id;
        }

        if (in_array($groupId, $groups) == false) {
            $user->joinGroups($groupId);
        }
    }

    public function removeGroup($userId, $groupId)
    {
        $userHandler = app('xe.user');
        $user = $userHandler->users()->with('groups', 'emails', 'accounts')->find($userId);
        $groups = [];
        foreach($user->getGroups() as $group) {
            $groups[] = $group->id;
        }

        if (in_array($groupId, $groups) == true) {
            $user->leaveGroups([$groupId]);
        }
    }
}
