<?php
namespace TypechoPlugin\CommentNtfy;

use Widget\Options;
use Widget\Notice;
use Typecho\Http\Client;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Submit;
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
        \Typecho\Plugin::factory('Widget_Service')->commentNtfyAsync = __CLASS__ . '::commentNtfyAsync'; // 异步接口
    }

    public static function deactivate()
    {
    }

    public static function getPluginOptions() {
        return Options::alloc()->plugin('CommentNtfy');
    }

    public static function config(Form $form)
    {
        Options::alloc()->to($options);
        $security = \Helper::security();

        $ntfyServer = new Text('ntfyServer', null, 'https://ntfy.sh/', 'ntfy 服务器 URL');
        $form->addInput($ntfyServer);

        $ntfyTopic = new Text('ntfyTopic', null, 'your-topic', 'ntfy topic');
        $form->addInput($ntfyTopic);

        $async = new Radio('async', ['0' => _t('不启用'), '1' => _t('启用')], '0', '异步发送通知');
        $form->addInput($async);

        $testButton = new Submit('send-test-notification');
        $testButton->value('发送测试通知');
        $testButton->input->setAttribute('class', 'btn');
        $testButton->input->setAttribute('formmethod', 'post');
        $testButton->input->setAttribute('formaction', $security->getAdminUrl('options-plugin.php?config=CommentNtfy&send-test-notification=1'));
        $form->addItem($testButton);

        $request = \Typecho\Request::getInstance();

        if ($request->isPost() && $request->is('send-test-notification=1')) {
            $security->protect();
            self::sendTestNotification();
            Options::alloc()->response->goBack();
        }
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function handleFinishComment($comment)
    {
        $pluginOptions = self::getPluginOptions();
        if ($pluginOptions->async) {
            Helper::requestService('commentNtfyAsync', [
                'coid' => $comment->coid,
            ]);
        } else {
            self::sendCommentNotification($comment);
        }
    }

    public static function commentNtfyAsync($data)
    {
        $coid = intval($data['coid']);
        $comment = Helper::widgetById('comments', $coid);
        if ($comment->have()) {
            self::fastEndResponse();
            self::sendCommentNotification($comment);
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

    public static function sendCommentNotification($comment)
    {
        Options::alloc()->to($options);
        $pluginOptions = self::getPluginOptions();

        $post = Helper::widgetById('contents', $comment->cid);
        $postTitle = $post->title;

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
            'message' => "文章: {$postTitle}\n评论者: {$comment->author} ({$comment->mail})\n评论内容: {$comment->text}\n状态: {$status}",
            'title' => $options->title . ' 有新的评论',
            'actions' => $actions,
        ];

        self::sendNtfy($payload);
    }

    public static function sendTestNotification()
    {
        Options::alloc()->to($options);
        $pluginOptions = self::getPluginOptions();

        $payload = [
            'topic' => $pluginOptions->ntfyTopic,
            'message' => '如果您看到这条通知，说明您的 CommentNtfy 插件配置正确',
            'title' => '测试通知',
        ];

        $client = self::sendNtfy($payload);
        $statusCode = $client->getResponseStatus();
        $responseBody = $client->getResponseBody();
        if ($statusCode >= 400) {
            Notice::alloc()->set("测试通知发送失败: [HTTP {$statusCode}] {substr($responseBody, 0, 512)}", 'error');
        } else {
            Notice::alloc()->set('测试通知发送成功', 'success');
        }
    }

    private static function sendNtfy($payload)
    {
        Options::alloc()->to($options);
        $pluginOptions = self::getPluginOptions();

        $client = Client::get();
        $client->setHeader('User-Agent', $options->generator)
            ->setTimeout(5)
            ->setJson($payload, Client::METHOD_PUT)
            ->send($pluginOptions->ntfyServer);

        return $client;
    }
}
