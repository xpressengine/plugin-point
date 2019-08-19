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
use Xpressengine\Plugin\AbstractPlugin;
use Xpressengine\Plugins\Board\Models\Board;
use Xpressengine\Plugins\Board\Modules\BoardModule;
use Xpressengine\Plugins\Comment\Models\Comment;
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
        app()->alias(Handler::class, 'point::handler');
    }

    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvent();

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

    protected function registerEvent()
    {
        // user - register
        intercept(
            'XeUser@create',
            'point@create',
            function ($target, $data, $token = null) {

                $user = $target($data, $token);

                app('point::handler')->executeAction('user_register', $user);

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
            app('point::handler')->executeAction('user_login', $user);
        });

        // board - write document
        intercept(
            '\Xpressengine\Plugins\Board\Handler@add',
            'point.board-write-document',
            function ($target, array $args, UserInterface $user, ConfigEntity $config) {

                $skip = false;

                if ($user instanceof Guest) {
                    $skip = true;
                }

                /** @var Board $boardDoc */
                $boardDoc = $target($args, $user, $config);

                if ($skip === false) {
                    app('point::handler')->executeAction(
                        'board.write-document.'.$boardDoc->getInstanceId(),
                        $user,
                        ['document_id' => $boardDoc->id, 'type' => 'create']
                    );
                }

                return $boardDoc;
            }
        );

        // board - restore document
        intercept(
            '\Xpressengine\Plugins\Board\Handler@restore',
            'point.board-restore-document',
            function ($target, Board $boardDoc, ConfigEntity $config) {

                /** @var Board $boardDoc */
                $target($boardDoc, $config);

                app('point::handler')->executeAction(
                    'board.write-document.'.$boardDoc->getInstanceId(),
                    $boardDoc->getUserId(),
                    ['document_id' => $boardDoc->id, 'type' => 'restore']
                );

                return $boardDoc;
            }
        );

        // board - delete document
        intercept(
            ['\Xpressengine\Plugins\Board\Handler@remove', '\Xpressengine\Plugins\Board\Handler@trash'],
            'point.board-delete-document',
            function ($target, Board $board, ConfigEntity $config) {
                $target($board, $config);

                $type = $target->getTargetMethodName();

                app('point::handler')->executeAction(
                    'board.delete-document.'.$board->getInstanceId(),
                    $board->getUserId(),
                    ['document_id' => $board->id, 'type' => $type]
                );
            }
        );

        // board - write comment
        intercept(
            ['Xpressengine\Plugins\Comment\Handler@create', 'Xpressengine\Plugins\Comment\Handler@restore'],
            'point.write-comment',
            function ($target, $inputs, $user = null) {
                /** @var Comment $comment */
                $comment = $target($inputs, $user);
                $boardDoc = Board::find($comment->target->targetId);

                if ($boardDoc == null) {
                    return $comment;
                }
                if ($boardDoc->type != BoardModule::getId()) {
                    return $comment;
                }

                $type = $target->getTargetMethodName();

                app('point::handler')->executeAction(
                    'board.write-comment.'.$boardDoc->getInstanceId(),
                    $comment->getAuthor(),
                    ['document_id' => $boardDoc->id, 'comment_id' => $comment->id, 'type' => $type]
                );

                return $comment;
            }
        );

        // board - delete or trash comment
        intercept(
            ['Xpressengine\Plugins\Comment\Handler@trash'/*, 'Xpressengine\Plugins\Comment\Handler@remove'*/],
            'point.delete-comment',
            function ($func, Comment $comment) {

                $result = $func($comment);

                if ($boardDoc = Board::find($comment->target->targetId)) {
                    if ($boardDoc->type != BoardModule::getId()) {
                        return $result;
                    }

                    app('point::handler')->executeAction(
                        'board.delete-comment.'.$boardDoc->getInstanceId(),
                        $comment->getAuthor(),
                        ['document_id' => $boardDoc->id, 'comment_id' => $comment->id]
                    );
                }

                return $result;
            }
        );
    }

    protected function route()
    {
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
                            ]
                        );
                        Route::get(
                            '{userId}',
                            [
                                'as' => 'point::setting.show',
                                'uses' => 'SettingController@show',
                            ]
                        );

                        Route::put(
                            'section',
                            [
                                'as' => 'point::section.update',
                                'uses' => 'SettingController@updateSection',
                            ]
                        );
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
        app('point::handler')->storeActionInfo('user_login', ['point'=> 10, 'title'=>'로그인']);
        app('point::handler')->storeActionInfo('user_register', ['point'=> 50, 'title'=>'가입']);

        // board
        app('point::handler')->storeActionInfo('board', ['point'=> 10, 'title'=>'게시판']);
        app('point::handler')->storeActionInfo('board.write-document', ['title'=>'게시판 글작성']);
        app('point::handler')->storeActionInfo('board.delete-document', ['title'=>'게시판 글삭제']);
        app('point::handler')->storeActionInfo('board.write-comment', ['title'=>'게시판 댓글작성']);
        app('point::handler')->storeActionInfo('board.delete-comment', ['title'=>'게시판 댓글삭제']);
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
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        // implement code

        return parent::checkUpdated();
    }
}
