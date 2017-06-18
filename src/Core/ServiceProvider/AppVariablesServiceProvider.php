<?php
/**
 * Created by PhpStorm.
 * User: mikolajczajkowsky
 * Date: 17.06.2017
 * Time: 16:39
 */

namespace Slice\Core\ServiceProvider;


use Slice\Container\Container;
use Slice\Container\ServiceProvider\ServiceProviderInterface;
use Slice\Core\AppVariables;

class AppVariablesServiceProvider implements ServiceProviderInterface
{

    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function register(Container $container)
    {
        $appVariables = new AppVariables();
        $appVariables
            ->setRootDir($this->params['rootDir'])
            ->setPublicDir($this->params['publicDir'])
            ->setEnvironment($this->params['environment']);

        $container->add('app', $appVariables);
    }
}