<?php
namespace SixMQ\Client\Network;

use SixMQ\Struct\Queue\Client\Auth\Login;
use SixMQ\Client\Exception\LoginFailException;

abstract class BaseClient implements IClient
{
    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username, $password)
    {
        $message = new Login($username, $password);
        $result = $this->sendMessage(new SendMessage($message));
        if(!$result)
        {
            return false;
        }
        $data = $result->getData();
        if($data->success)
        {
            return true;
        }
        else
        {
            throw new LoginFailException($data->error);
        }
    }

}