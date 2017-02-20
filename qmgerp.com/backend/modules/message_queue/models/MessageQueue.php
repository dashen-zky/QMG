<?php
namespace backend\modules\message_queue\models;
use macklus\SimpleQueue\SimpleQueue;
use Yii;
use macklus\SimpleQueue\SimpleQueueMessage;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/30 0030
 * Time: 下午 10:12
 */
class MessageQueue extends SimpleQueue
{
    public function init()
    {
        $this->queues = [
            Yii::$app->getUser()->getIdentity()->getId(),
        ];
        $this->table = 'com_message_queue';
        $this->persistent = true;
        $this->duplicate_jobs = false;
        parent::init();
    }
    
    public function end(array $message)
    {
        foreach ($message as $m) {
            $this->connection->createCommand('UPDATE ' . $this->getTableName() . ' SET state=:state,end=NOW() WHERE id=:id')
                ->BindValues(['state' => self::STATE_ENDED, 'id' => $m->id])->execute();
        }
    }
}