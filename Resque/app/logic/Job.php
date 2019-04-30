<?php
namespace app\logic;

use app\db\Order;
use app\util\MailBox;
use app\util\Sms;
use app\util\WxMessage;
use app\util\WxPay;
use Serve\Colors\Color;
use Serve\Colors\ColorText;

class Job
{
    /**
     * @var string
     *
     */
    private $queue = 'delayer::order_queue';

    /**
     * @param $server
     * @return string
     * 消息队列中拿取数据
     */
    public function dequeue($queue): ?string
    {
        $message = $queue->bPop($this->queue, 2);
        if ($message !== false) {
            return $message->body;
        }
        return null;
    }

    /**
     * @param $server
     * @param $taskId
     * @param $reactorId
     * @param $data
     * 队列中拿到的数据,进行业务逻辑操作
     */
    public function business($pdo, $data): void
    {
        MailBox::send();
//         待支付订单,直接取消关闭
        $order = json_decode($data, true);
        $orderSn = $order['order_sn'];
        (new Order($pdo))->getOrder($orderSn)->cancelled();
    }
}