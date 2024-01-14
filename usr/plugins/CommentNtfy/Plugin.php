<?php
namespace TypechoPlugin\CommentNtfy;

use Widget\Options;
use Typecho\Http\Client;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Response;
use Utils\Helper;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 推送评论到 ntfy
 *
 * @package CommentNtfy
 * @version 1.0.0
 * @author imkero
 * @link https://imkero.net/
 */
class Plugin implements PluginInterface
{
    public static function activate()
    {
        \Typecho\Plugin::factory('Widget_Feedback')->finishComment = __CLASS__ . '::handleFinishComment';
        \Typecho\Plugin::factory('Widget_Service')->commentNtfyAsync = __CLASS__ . '::commentNtfyAsync';//异步接口
    }

    public static function deactivate()
    {
    }

    public static function config(Form $form)
    {
      $ntfyServer = new Text('ntfyServer', null, 'https://ntfy.sh/', 'ntfy 服务器 URL');
      $form->addInput($ntfyServer);

      $ntfyTopic = new Text('ntfyTopic', null, 'your-topic', 'ntfy topic');
      $form->addInput($ntfyTopic);

      $async = new Radio('async', array('0' => _t('不启用'), '1' => _t('启用'),), '0', '异步发送通知');
      $form->addInput($async);
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function handleFinishComment($comment)
    {
        $pluginOptions = Options::alloc()->plugin('CommentNtfy');
        if ($pluginOptions->async) {
            Helper::requestService('commentNtfyAsync', [
                'coid' => $comment->coid,
            ]);
        } else {
            self::sendNotification($comment);
        }
    }

    public static function commentNtfyAsync($data)
    {
        $coid = intval($data['coid']);
        $comment = Helper::widgetById('comments', $coid);
        if ($comment->have()) {
            // self::fastEndResponse();
            self::sendNotification($comment);
        }
    }

    public static function fastEndResponse() {
        $response = Response::getInstance();
        $response->clean();
        $response->setHeader('Content-Length', '0');
        $response->setHeader('Connection', 'close');

        $level = ob_get_level();
        for ($i = 0; $i < $level; $i++) ob_end_clean();
        flush();

        ob_start(function ($content) {
            return '';
        });
    }

    public static function sendNotification($comment)
    {
        Options::alloc()->to($options);
        $pluginOptions = Options::alloc()->plugin('CommentNtfy');

        $statusMap = [
            'approved' => '通过',
            'waiting' => '待审核',
            'spam' => '垃圾'
        ];
        $status = isset($statusMap[$comment->status]) ? $statusMap[$comment->status] : '未知';

        $actions = [];

        if ($comment->status == 'approved') {
            $actions[] = [
                'action' => 'view',
                'label' => '查看评论',
                'url' => $comment->permalink
            ];
        }

        $actions[] = [
            'action' => 'view',
            'label' => '管理评论',
            'url' => $options->adminUrl . 'manage-comments.php?status=' . $comment->status
        ];

        $payload = [
            'topic' => $pluginOptions->ntfyTopic,
            'message' => "评论者：{$comment->author} ({$comment->mail})\n评论内容：{$comment->text}\n状态：{$status}",
            'title' => $options->title . ' 有新的评论',
            'actions' => $actions,
        ];

        $client = Client::get();
        $client->setHeader('User-Agent', $options->generator)
            ->setTimeout(2)
            ->setJson($payload, Client::METHOD_PUT)
            ->send($pluginOptions->ntfyServer);
    }
}
