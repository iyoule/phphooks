# phphooks
phphooks


使用方法
所有需要hook的类。继承Object此类

实例化时候。使用 {类}::newInstanceArgs
进行实例化

```php
include "../hook/Init.php";
class hookmethod extends Object
{
    public function __construct()
    {
        //hook本类的fn1
        Hook::set(['fn1'], function () {
            var_dump('hook->fn1');
            return Hook::callNextStep();
        });
        //hook指定实例的指定方法
        Hook::set([$this, 'fn2'], function () {
            var_dump('hook->fn2');
            Hook::callNextStep();
        });
    }
    public function fn1()
    {
        var_dump(__METHOD__);
    }
    public function fn2()
    {
        var_dump(__METHOD__);
    }
    public function fn3()
    {
        var_dump(__METHOD__);
    }
}

//使用newInstance进行实例化
$hook = hookmethod::newInstance();
$hook->fn1();
$hook->fn2();

Hook::set([$hook, 'f3'], function () {
    var_dump('hook->fn3');
    Hook::callNextStep();
});

$hook->fn3();

```