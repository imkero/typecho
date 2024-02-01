<?php
namespace TypechoPlugin\MetaCountFix;

use Widget\Options;
use Widget\Notice;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Submit;
use Typecho\Widget\Helper\Form\Element\Hidden;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 文章分类、标签计数修复
 *
 * @package MetaCountFix
 * @version 1.0.0
 * @author imkero
 * @link https://imkero.net/
 */
class Plugin implements PluginInterface
{
    public static function activate()
    {
    }

    public static function deactivate()
    {
    }

    public static function config(Form $form)
    {
        Options::alloc()->to($options);
        $security = \Helper::security();

        $hidden = new Hidden('dummy', null, '');
        $form->addInput($hidden);

        $fixButton = new Submit('do-fix');
        $fixButton->value('修复文章计数');
        $fixButton->input->setAttribute('class', 'btn');
        $fixButton->input->setAttribute('formmethod', 'post');
        $fixButton->input->setAttribute('formaction', $security->getAdminUrl('options-plugin.php?config=MetaCountFix&do-fix=1'));
        $form->addItem($fixButton);

        $request = \Typecho\Request::getInstance();

        if ($request->isPost() && $request->is('do-fix=1')) {
            $security->protect();
            $result = self::fixMetaCount();

            echo <<<EOF
<ul class="typecho-option">
<li>
<label class="typecho-label">修复结果</label>
<textarea>
EOF;
            echo '共更新 ', count($result), " 个分类/标签\n";
            foreach ($result as $item) {
                echo htmlspecialchars($item['type'] . ' ' . $item['name'] . ': ' . $item['oldCount'] . ' => ' . $item['newCount']) . "\n";
            }
echo <<<EOF
</textarea>
</li>
</ul>
EOF;
        }
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function fixMetaCount()
    {
        try {
            $result = [];
            $db = \Typecho\Db::get();

            $metas = $db->fetchAll($db->select()
                ->from('table.metas')
                ->where('(type = ? OR type = ?)', 'category', 'tag'));

            foreach ($metas as $meta) {
                $oldCount = $meta['count'];
                $newCount = $db->fetchObject($db->select(array('COUNT(table.relationships.cid)' => 'num'))
                    ->from('table.relationships')
                    ->join('table.contents', 'table.relationships.cid = table.contents.cid')
                    ->where('table.contents.status = ?', 'publish')
                    ->where('table.relationships.mid = ?', $meta['mid']))->num;

                if ($oldCount !== $newCount) {
                    $db->query($db->update('table.metas')
                        ->rows(array('count' => $newCount))
                        ->where('mid = ?', $meta['mid']));
                    $result[] = [
                        'type' => $meta['type'],
                        'name' => $meta['name'],
                        'oldCount' => $oldCount,
                        'newCount' => $newCount,
                    ];
                }
            }

            Notice::alloc()->set('修复文章计数成功', 'success');

            return $result;
        } catch (\Exception $e) {
            if (defined('__TYPECHO_DEBUG__') && __TYPECHO_DEBUG__) {
                throw $e;
            }

            Notice::alloc()->set('修复文章计数失败', 'error');

            return [];
        }
    }
}
