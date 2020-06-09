<?php
/**
 * Plugin.php
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

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Route;
use Schema;
use Xpressengine\Config\ConfigEntity;
use Xpressengine\Menu\Models\MenuItem;
use Xpressengine\Plugin\AbstractPlugin;
use Xpressengine\Plugins\Board\Models\Board;
use Xpressengine\Plugins\Board\Modules\BoardModule;
use Xpressengine\Plugins\Comment\Models\Comment;
use Xpressengine\Plugins\Point\Models\Point;
use Xpressengine\User\UserInterface;
use XeToggleMenu;

/**
 * Class Plugin
 *
 * @category    Point
 * @package     Xpressengine\Plugins\Point\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class Plugin extends AbstractPlugin
{

    public function register()
    {
        app()->singleton(Handler::class, function ($app) {
            $proxyClass = app('xe.interception')->proxy(Handler::class, 'Point');
            return new $proxyClass($this, app('xe.config'));
        });
        app()->alias(Handler::class, 'point::handler'); // deprecated
        app()->alias(Handler::class, 'xe.point.handler');
    }

    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvent();
        $this->registerUserMacro();
        $this->registerDocumentMacro();

        $this->route();
    }

    /**
     * 플러그인의 설정페이지 주소를 반환한다.
     * 플러그인 목록에서 플러그인의 '관리' 버튼을 누를 경우 이 페이지에서 반환하는 주소로 연결된다.
     *
     * @return string
     */
    public function getSettingsURI()
    {
        return route('point::setting.index');
    }

    protected function registerUserMacro()
    {
        \Xpressengine\User\Models\User::macro('point', function() {
            return $this->belongsTo( Point::class, 'id', 'user_id');
        });
        \Xpressengine\User\Models\User::macro('point_level', function() {
            $level = 0;
            if ($this->point != null) {
                $level = $this->point->level;
            }
            return $level;
        });
        \Xpressengine\User\Models\User::macro('point_level_icon', function() {
            $handler = app('xe.point.handler');
            return $handler->getIcon($this->point_level);
        });
    }

    protected function registerDocumentMacro()
    {
        // for document
        \Xpressengine\Document\Models\Document::macro('point', function() {
            return $this->belongsTo( Point::class, 'id', 'user_id');
        });
        \Xpressengine\Document\Models\Document::macro('point_level', function() {
            $level = 0;
            if ($this->point != null) {
                $level = $this->point->level;
            }
            return $level;
        });
        \Xpressengine\Document\Models\Document::macro('point_level_icon', function() {
            $handler = app('xe.point.handler');
            return $handler->getIcon($this->point_level);
        });

        // for board
        \Xpressengine\Plugins\Board\Models\Board::macro('point', function() {
            return $this->belongsTo( Point::class, 'id', 'user_id');
        });
        \Xpressengine\Plugins\Board\Models\Board::macro('point_level', function() {
            $level = 0;
            if ($this->point != null) {
                $level = $this->point->level;
            }
            return $level;
        });
        \Xpressengine\Plugins\Board\Models\Board::macro('point_level_icon', function() {
            $handler = app('xe.point.handler');
            return $handler->getIcon($this->point_level);

        });

        // for comment
        \Xpressengine\Plugins\Comment\Models\Comment::macro('point', function() {
            return $this->belongsTo( Point::class, 'id', 'user_id');
        });
        \Xpressengine\Plugins\Comment\Models\Comment::macro('point_level', function() {
            $level = 0;
            if ($this->point != null) {
                $level = $this->point->level;
            }
            return $level;
        });
        \Xpressengine\Plugins\Comment\Models\Comment::macro('point_level_icon', function() {
            $handler = app('xe.point.handler');
            return $handler->getIcon($this->point_level);
        });
    }

    protected function registerEvent()
    {
        // user - register
        intercept(
            'XeUser@create',
            'point@create',
            function ($target, $data, $token = null) {
                $user = $target($data, $token);

                // start point
                $pointHandler = app('point::handler');
                $action = 'user_register';
                $pointHandler->executeAction($action, $user);

                return $user;
            }
        );

        // user - login
        app('events')->listen('auth.login', function ($user) {
            if ($user->loginAt === null) {
                return $user;
            }
            if ($user->loginAt->isSameDay(Carbon::now())) {
                return $user;
            }

            // start point
            $pointHandler = app('point::handler');
            $action = 'user_login';
            if ($pointHandler->checkAction($action, $user) == false) {
                $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                    [], 500
                );
                $exception->setMessage('[포인트 부족] login 할 수 없습니다.');
                throw $exception;
            }

            $pointHandler->executeAction($action, $user);
        });

        // board - write document
        intercept(
            '\Xpressengine\Plugins\Board\Handler@add',
            'point.board-write-document',
            function ($func, array $args, UserInterface $user, ConfigEntity $config) {
                \XeDB::beginTransaction();
                /** @var Board $board */
                $board = $func($args, $user, $config);

                $pointHandler = app('point::handler');

                $instanceId = $args['instance_id'];

                $user = \Auth::user();
                $action = 'board.write-document.'.$instanceId;
                if ($pointHandler->checkAction($action, $user) == false) {
                    $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                        [], 500
                    );
                    $exception->setMessage('[포인트 부족] 글을 등록할 수 없습니다.');
                    throw $exception;
                }

                $pointHandler->executeAction(
                    $action,
                    $user,
                    ['instance_id' => $args['instance_id'], 'type' => 'create']
                );

                // check file count, upload file point
                $fileCount = 0;
                if (isset($args['_files'])) {
                    $fileCount = count($args['_files']);
                }

                if ($fileCount > 0) {
                    $action = 'board.upload-file.'.$instanceId;
                    if ($pointHandler->checkAction($action, $user) == false) {
                        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                            [], 500
                        );
                        $exception->setMessage('[포인트 부족] 업로드할 수 없습니다.');
                        throw $exception;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $user,
                        ['instance_id' => $args['instance_id'], 'file_count' => $fileCount]
                    );
                }

                \XeDB::commit();
                return $board;
            }
        );

        // board - restore document, @deprecated
        intercept(
            '\Xpressengine\Plugins\Board\Handler@restore',
            'point.board-restore-document',
            function ($func, Board $board, ConfigEntity $config) {
                \XeDB::beginTransaction();
                /** @var Board $boardDoc */
                $func($board, $config);

                app('point::handler')->executeAction(
                    'board.write-document.'.$board->getInstanceId(),
                    $board->getUserId(),
                    ['document_id' => $board->id, 'type' => 'restore']
                );
                \XeDB::commit();
            }
        );

        // board - delete document
        intercept(
            ['\Xpressengine\Plugins\Board\Handler@remove', '\Xpressengine\Plugins\Board\Handler@trash'],
            'point.board-delete-document',
            function ($func, Board $board, ConfigEntity $config) {
                \XeDB::beginTransaction();
                $pointHandler = app('point::handler');

                if($board->type != 'module/board@board') {
                    \XeDB::commit();
                    return;
                }
                $instanceId = $board->instance_id;

                $user = \Auth::user();
                $action = 'board.delete-document.'.$instanceId;
                if ($pointHandler->checkAction($action, $user) == false) {
                    $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                        [], 500
                    );
                    $exception->setMessage('[포인트 부족] 글을 삭제할 수 없습니다.');
                    throw $exception;
                }

                $pointHandler->executeAction(
                    $action,
                    $user,
                    ['document_id' => $board->id, 'type' => 'remove']
                );

                $func($board, $config);
                \XeDB::commit();
            }
        );

        // board - read document
        intercept(
            '\Xpressengine\Plugins\Board\Handler@incrementReadCount',
            'point.read-document',
            function ($func, Board $board, UserInterface $user) {
                \XeDB::beginTransaction();
                $currentReadCount = $board->read_count;
                $func($board, $user);

                // 조회수 변경되지 않음, 이미 읽은 글
                if ($currentReadCount == $board->read_count) {
                    \XeDB::commit();
                    return;
                }

                $pointHandler = app('point::handler');

                if($board->type != 'module/board@board') {
                    \XeDB::commit();
                    return;
                }
                $instanceId = $board->instance_id;

                // check url
                $route = \Route::getCurrentRoute();
                $parts = [];
                if ($route != null) {
                    $parts = explode('@', $route->getActionName());
                    $parts = explode('\\', array_shift($parts));
                }

                if (array_pop($parts) == 'BoardModuleController') {
                    $user = \Auth::user();
                    $action = 'board.read-document.'.$instanceId;
                    if ($pointHandler->checkAction($action, $user) == false) {
                        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                            [], 500
                        );
                        $exception->setMessage('[포인트 부족] 글을 조회할 수 없습니다.');
                        throw $exception;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $user,
                        ['document_id' => $board->id, 'type' => 'read']
                    );
                }

                \XeDB::commit();
            }
        );

        // board - vote 추천, 비추천
        intercept(
            '\Xpressengine\Plugins\Board\Handler@incrementVoteCount',
            'point.vote-increment-document',
            function ($func, Board $board, UserInterface $user, $option, $point) {
                \XeDB::beginTransaction();
                $func($board, $user, $option, $point);

                $pointHandler = app('point::handler');

                if($board->type != 'module/board@board') {
                    \XeDB::commit();
                    return;
                }
                $instanceId = $board->instance_id;

                // check url
                $route = \Route::getCurrentRoute();
                $parts = [];
                if ($route != null) {
                    $parts = explode('@', $route->getActionName());
                    $parts = explode('\\', array_shift($parts));
                }
                if (array_pop($parts) == 'BoardModuleController') {

                    if ($option == 'assent') {
                        $action = 'board.receive-assent-document.'.$instanceId;
                    } elseif ($option == 'dissent') {
                        $action = 'board.receive-dissent-document.'.$instanceId;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $board->user_id,
                        ['document_id' => $board->id]
                    );
                }

                \XeDB::commit();
            }
        );

        // comment - write comment
        intercept(
            ['Xpressengine\Plugins\Comment\Handler@create', 'Xpressengine\Plugins\Comment\Handler@restore'],
            'point.write-comment',
            function ($target, $inputs, $user = null) {
                \XeDB::beginTransaction();

                /** @var Comment $comment */
                $comment = $target($inputs, $user);
                $boardDoc = Board::find($inputs['target_id']);

                $pointHandler = app('point::handler');

                if ($boardDoc == null) {
                    \XeDB::commit();
                    return $comment;
                } elseif($boardDoc->type != 'module/board@board') {
                    \XeDB::commit();
                    return $comment;
                }
                $instanceId = $boardDoc->instance_id;

                $user = \Auth::user();
                $action = 'board.write-comment.'.$instanceId;
                if ($pointHandler->checkAction($action, $user) == false) {
                    $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                        [], 500
                    );
                    $exception->setMessage('[포인트 부족] 댓글을 등록할 수 없습니다.');
                    throw $exception;
                }

                $pointHandler->executeAction(
                    $action,
                    $user,
                    ['document_id' => $boardDoc->id, 'comment_id' => $comment->id, 'type' => 'create']
                );
                \XeDB::commit();
                return $comment;
            }
        );

        // comment - trash comment, 코멘트에서 trash 하고 delete 함.. 중복 처리하기 때문에 remove 는 처리 안함
        intercept(
            'Xpressengine\Plugins\Comment\Handler@trash',
            'point.trash-comment',
            function ($func, Comment $comment) {
                \XeDB::beginTransaction();

                $targetId = $comment->target->target_id;

                $result = $func($comment);

                $board = Board::find($targetId);

                $pointHandler = app('point::handler');

                if ($board != null && $board->type == 'module/board@board') {
                    $instanceId = $board->instance_id;
                    $user = \Auth::user();
                    $action = 'board.delete-comment.'.$instanceId;
                    if ($pointHandler->checkAction($action, $user) == false) {
                        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                            [], 500
                        );
                        $exception->setMessage('[포인트 부족] 댓글을 삭제할 수 없습니다.');
                        throw $exception;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $user,
                        ['document_id' => $board->id, 'comment_id' => $comment->id]
                    );
                }

                \XeDB::commit();

                return $result;
            }
        );

        // board - upload file when upload from editor @deprecated
        intercept(
            ['Xpressengine\Storage\Storage@upload'],
            'point.board-upload-file-editor',
            function ($func, $uploadedFile, $path) {
                \XeDB::beginTransaction();

                $result = $func($uploadedFile, $path);
                // start point
                $pointHandler = app('point::handler');

                // get instance id
                $instanceId = request()->segment(3);
                $menuItem = MenuItem::where('id', $instanceId)->first();

                if ($menuItem != null && $menuItem->type == 'board@board') {
                    $user = \Auth::user();
                    $action = 'board.upload-file.'.$instanceId;
                    if ($pointHandler->checkAction($action, $user) == false) {
                        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                            [], 500
                        );
                        $exception->setMessage('[포인트 부족] 업로드 할 수 없습니다.');
                        throw $exception;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $user,
                        ['instance_id' => $instanceId]
                    );
                }

                \XeDB::commit();
                return $result;
            }
        );

        // board - download file
        intercept(
            ['Xpressengine\Storage\Storage@download'],
            'point.board-download-file',
            function ($func, $file) {
                \XeDB::beginTransaction();

                $result = $func($file);

                // start point
                $pointHandler = app('point::handler');

                // get instance id
                $instanceId = request()->segment(3);
                $menuItem = MenuItem::where('id', $instanceId)->first();

                $isImage = false;
                if ($file != null && explode('/', $file->mime)[0] == 'image') {
                    $isImage = true;
                }

                // 이미지는 제외
                if ($menuItem != null && $menuItem->type == 'board@board' && $isImage == false) {
                    $user = \Auth::user();
                    $action = 'board.download-file.'.$instanceId;
                    if ($pointHandler->checkAction($action, $user) == false) {
                        $exception = new \Xpressengine\Support\Exceptions\HttpXpressengineException(
                            [], 500
                        );
                        $exception->setMessage('[포인트 부족] 다운로드 할 수 없습니다.');
                        throw $exception;
                    }

                    $pointHandler->executeAction(
                        $action,
                        $user,
                        ['instance_id' => $instanceId, 'file_id' => $file->id, 'mime' => $file->mime,]
                    );
                }

                \XeDB::commit();
                return $result;
            }
        );
    }

    protected function route()
    {
        // settings menu 등록
        $menus = [
            'user.point' => [
                'title' => 'point::point',
                'display' => true,
                'description' => '',
                'ordering' => 1000
            ],
            'user.point.log' => [
                'title' => 'point::pointEarnUseLog',
                'display' => true,
                'description' => '',
                'ordering' => 10001
            ],
            'user.point.config' => [
                'title' => 'point::pointSetup',
                'display' => true,
                'description' => '',
                'ordering' => 10003
            ],
        ];
        foreach ($menus as $id => $menu) {
            app('xe.register')->push('settings/menu', $id, $menu);
        }

        Route::settings(
            $this->getId(),
            function () {
                Route::group(
                    ['namespace' => 'Xpressengine\Plugins\Point\Controllers'],
                    function () {
                        Route::get(
                            '/',
                            [
                                'as' => 'point::setting.index',
                                'uses' => 'SettingController@index',
                                'settings_menu' => 'user.point.config',
                            ]
                        );
                        Route::get('/instance', ['as' => 'point::setting.instance', 'uses' => 'SettingController@instance',]);
                        Route::get('/user', ['as' => 'point::setting.user', 'uses' => 'SettingController@user',]);
                        Route::post('/user/point/update', ['as' => 'point::setting.user.point.update', 'uses' => 'SettingController@updateUserPoint',]);

                        Route::get(
                            '/logs',
                            [
                                'as' => 'point::setting.logs',
                                'uses' => 'SettingController@logs',
                                'settings_menu' => 'user.point.log',
                            ]
                        );

                        Route::put('/config/update', ['as' => 'point::config.update', 'uses' => 'SettingController@updateConfig',]);
                        Route::put('/section/update', ['as' => 'point::section.update', 'uses' => 'SettingController@updateSection',]);
                        Route::put('/group/update', ['as' => 'point::group.update', 'uses' => 'SettingController@updateGroup',]);
                        Route::put('/level_point/update', ['as' => 'point::level_point.update', 'uses' => 'SettingController@updateLevelPoint',]);
                        Route::post('/user_point/update', ['as' => 'point::user_point.update', 'uses' => 'SettingController@updateUserPoint',]);

                        Route::get('/{userId}', ['as' => 'point::setting.show', 'uses' => 'SettingController@show',]);

                    }
                );
            }
        );
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        app('xe.config')->set('point', []);

        // user
        app('point::handler')->storeActionInfo('user_login', ['point'=> 0, 'title'=>'xe::login']);
        app('point::handler')->storeActionInfo('user_register', ['point'=> 0, 'title'=>'xe::signUp']);

        // board
        app('point::handler')->storeActionInfo('board', ['point'=> 0, 'title'=>'board::board']);
        app('point::handler')->storeActionInfo('board.write-document', ['title'=>'point::articleStore']);
        app('point::handler')->storeActionInfo('board.delete-document', ['title'=>'point::articleDestroy']);
        app('point::handler')->storeActionInfo('board.write-comment', ['title'=>'point::commentStore']);
        app('point::handler')->storeActionInfo('board.delete-comment', ['title'=>'point::commentDestroy']);

        // version 1.0.2
        app('xe.config')->set('point.group', []);
        app('xe.config')->set('point.level_point', []);
        app('point::handler')->storeActionInfo('board.upload-file', ['title'=>'point::uploadFile']);
        app('point::handler')->storeActionInfo('board.download-file', ['title'=>'point::downloadFile']);
        app('point::handler')->storeActionInfo('board.read-document', ['title'=>'point::readDocument']);
        app('point::handler')->storeActionInfo('board.receive-assent-document', ['title'=>'point::receiveAssentDocument']);
        app('point::handler')->storeActionInfo('board.receive-dissent-document', ['title'=>'point::receiveDissentDocument']);
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        if (!Schema::hasTable('point')) {
            Schema::create(
                'point',
                function (Blueprint $table) {
                    $table->engine = "InnoDB";

                    $table->string('user_id', 36);
                    $table->bigInteger('point');
                    $table->bigInteger('level')->default(0);
                    $table->timestamp('created_at')->index();
                    $table->timestamp('updated_at')->index();
                    $table->primary('user_id');
                }
            );
        }

        if (!Schema::hasTable('point_log')) {
            Schema::create(
                'point_log',
                function (Blueprint $table) {
                    $table->engine = "InnoDB";

                    $table->increments('id');
                    $table->string('user_id', 36);
                    $table->string('action', 200);
                    $table->bigInteger('point');
                    $table->string('content');
                    $table->timestamp('created_at')->index();
                    $table->timestamp('updated_at');
                    $table->index('user_id');
                }
            );
        }

        $toggleMenuType = 'user';
        $activates = XeToggleMenu::getActivated($toggleMenuType);
        if (array_key_exists(UserMenus\PointItem::getId(), $activates) == false) {
            $activates[UserMenus\PointItem::getId()] = UserMenus\PointItem::class;
        }

        /** @var \Xpressengine\Translation\Translator $trans */
        $trans = app('xe.translator');
        $trans->putFromLangDataSource('point', base_path('plugins/point/langs/lang.php'));
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        if (!Schema::hasTable('point')) {
            return false;
        }
        if (!Schema::hasTable('point_log')) {
            return false;
        }
        return true;
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        // implement code
        if ($this->hasLevelFunction() == false) {
            $this->updateLevelFunction();
        }
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        if ($this->hasLevelFunction() == false) {
            return false;
        }

        return parent::checkUpdated();
    }

    /**
     * 1.0.2 에서 레벨 기능 추가
     *
     * @return bool
     */
    protected function hasLevelFunction()
    {
        if (!Schema::hasColumn('point', 'level')) {
            return false;
        }

        return true;
    }

    protected function updateLevelFunction()
    {
        if (!Schema::hasColumn('point', 'level')) {
            Schema::table('point', function (Blueprint $table) {
                $table->bigInteger('level')->default(0);
            });
        }

        app('xe.config')->set('point.group', []);
        app('xe.config')->set('point.level_point', []);
        app('point::handler')->storeActionInfo('board.upload-file', ['point'=> 0, 'title'=>'point::uploadFile']);
        app('point::handler')->storeActionInfo('board.download-file', ['point'=> 0, 'title'=>'point::downloadFile']);
        app('point::handler')->storeActionInfo('board.read-document', ['point'=> 0, 'title'=>'point::readDocument']);
        app('point::handler')->storeActionInfo('board.receive-assent-document', ['point'=> 0, 'title'=>'point::receiveAssentDocument']);
        app('point::handler')->storeActionInfo('board.receive-dissent-document', ['point'=> 0, 'title'=>'point::receiveDissentDocument']);
    }
}
