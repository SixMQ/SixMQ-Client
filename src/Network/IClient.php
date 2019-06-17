<?php
namespace SixMQ\Client\Network;

interface IClient
{
    /**
     * 连接服务器
     *
     * @return boolean
     */
    public function connect();

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close();

    /**
     * 是否已连接
     *
     * @return boolean
     */
    public function isConnected();

    /**
     * 发送消息
     *
     * @param \SixMQ\Client\Network\ISendMessage $message
     * @return IRecvMessage|boolean
     */
    public function sendMessage(\SixMQ\Client\Network\ISendMessage $message);

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username, $password);

}