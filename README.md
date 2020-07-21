# Token
这个是一个基于[hyperf](https://github.com/hyperf/hyperf "hyperf")的token组件。

**特性：**
1. 本token组件类似于session，服务端存储数据，token下发给客户端
2. 本token组件设计了一个双层数据存储的结构，一个唯一id可以存储数据，根据该id生成的token也可以存储数据。
   此特性可以实现多设备登录、互剔、多设备共享数据等。
3. 根据token只读数据时，不会产生落库操作。
4. 根据token对数据进行读写，不会改变token的有效期，需要实现token刷新的接口。

## 安装

**下载包** `composer require buexplain/token`

**发布token组件的配置** `php bin/hyperf.php vendor:publish buexplain/token`

**使用**

路由代码：

```php
use Hyperf\HttpServer\Router\Router;
//签发token
Router::post('/in', \App\Controller\IndexController::class.'@in');
Router::addGroup('',function () {
    //销毁token
    Router::get('/out', \App\Controller\IndexController::class.'@out');
    //刷新token
    Router::post('/refresh', \App\Controller\IndexController::class.'@refresh');
}, [
    'middleware' => [
        //先执行token初始化的中间件
        \Token\Middleware\TokenMiddleware::class,
        //后执行token权限校验的中间件
        \Token\Middleware\AuthMiddleware::class,
    ]
]);
```

控制器代码：

```php
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Di\Annotation\Inject;

class IndexController
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    protected static function getToken(): \Token\Contract\TokenInterface
    {
        /**
         * 通过容器拿token代理对象
         * @var $token \Token\Contract\TokenInterface
         */
        $token = \Hyperf\Utils\ApplicationContext::getContainer()->get(\Token\Contract\TokenInterface::class);
        return $token;
    }

    /**
     *  签发
     */
    public function in()
    {
        //假设用户唯一id
        $userId = 1;
        /**
         * @var $manager \Token\TokenManager
         */
        $manager = \Hyperf\Utils\ApplicationContext::getContainer()->get(\Token\TokenManager::class);
        $token = $manager->start($this->request);
        //设置用户唯一id
        $token->setId((string)$userId);
        //根据用户唯一id载入数据
        $token->load();
        //获取客户端传递的设备信息
        $device = $this->request->query('device', 'pc');
        //判断是否已经登录了此类设备
        $names = $token->getNames();
        foreach ($names as $name) {
            if($name->get('device') == $device) {
                throw new \Token\Exception\UnauthorizedException("您已经登录了设备：{$device}");
            }
        }
        //记录用户最后登录时间与设备
        $token->set('last_login_time', time());
        $token->set('last_login_device', $device);
        //记录用户本次登录的设备
        $token->getName()->set('device', $device);
        //构造返回值
        $data = ['token'=>(string)$token->getName(), 'expire'=>$token->getName()->expire(), 'sysTime'=>time()];
        //getName后主动落库
        $token->save();
        //返回
        return [
            'code'=>0,
            'message'=>'success',
            'data'=>$data,
        ];
    }
    
    /**
     *  销毁
     */
    public function out()
    {
        //销毁所有的设备的token
        self::getToken()->destroyAll();
        return [
            'code'=>0,
            'message'=>'success',
        ];
    }
    
    /**
     *  刷新
     */    
    public function refresh()
    {
        $token = self::getToken();
        $token->getName()->refresh();
        $data = ['token'=>(string)$token->getName(), 'expire'=>$token->getName()->expire(), 'sysTime'=>time()];
        return [
            'code'=>0,
            'message'=>'success',
            'data'=>$data,
        ];
    }
}
```

## License
[Apache-2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
