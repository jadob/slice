<?php
declare(strict_types=1);

namespace Jadob\Contracts\Core;


interface UserInterface
{

    /**
     * @return string[]
     */
    public function getRoles();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @return mixed
     */
    public function getUsername();

}