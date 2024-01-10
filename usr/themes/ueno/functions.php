<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class Ueno_Form_WriteStaticCSS extends \Typecho\Widget\Helper\Form\Element
{
    public function __construct()
    {
        parent::__construct('writeStaticCSS', NULL, NULL, '静态 CSS', NULL);
    }

    /**
     * 初始化当前输入项
     *
     * @param string|null $name 表单元素名称
     * @param array|null $options 选择项
     * @return Layout|null
     */
    public function input(?string $name = NULL, array $options = NULL): ?\Typecho\Widget\Helper\Layout
    {
        \Widget\Options::alloc()->to($options);
        $security = Helper::security();

        $cssFilePath = __DIR__ . '/style.css';
        $cssExist = @file_exists($cssFilePath);
        $cssFileModTime = $cssExist ? @filemtime($cssFilePath) : 0;
        $cssUrl = \Typecho\Common::url('style.css', $options->themeUrl);

        $writeButton = new Typecho_Widget_Helper_Layout(
            'button',
            [
                'class' => 'btn primary btn-xs ueno-theme-config-action',
                'data-action' => 'ajax',
                'data-url' => $security->getAdminUrl('options-theme.php?ueno_action=write-static-css')
            ]
        );
        $writeButton->html('写入静态 CSS');
        $this->container($writeButton);

        $previewButton = new Typecho_Widget_Helper_Layout(
            'button',
            array(
                'class' => 'btn btn-xs ueno-theme-config-action',
                'style' => 'margin-left: 8px',
                'data-action' => 'open',
                'data-url' => $security->getAdminUrl('options-theme.php?ueno_action=preview-static-css')
            )
        );
        $previewButton->html('下载静态 CSS');
        $this->container($previewButton);

        $statusText = new Typecho_Widget_Helper_Layout('p', [
            'class' => 'description',
        ]);
        $statusText->html($cssExist
            ? 'CSS 文件存在，'
             . '修改时间：'
             . date('Y-m-d H:i:s', $cssFileModTime)
             . '，<a href="' . $cssUrl . '" target="_blank">查看</a>'
            : 'CSS 文件不存在');
        $this->container($statusText);

        $script = new Typecho_Widget_Helper_Layout('script');
        $script->html(self::ACTION_SCRIPT);
        $this->container($script);

        return NULL;
    }

    /**
     * 设置表单项默认值
     *
     * @param mixed $value 表单项默认值
     */
    protected function inputValue($value)
    {
    }

    const ACTION_SCRIPT = <<<SCRIPT
document.addEventListener('DOMContentLoaded', function () {
    $('.ueno-theme-config-action').click(function () {
        const action = $(this).data('action');
        if (action === 'open') {
            window.open($(this).data('url'), '_blank');
        } else if (action === 'ajax') {
            $.post(
                $(this).data('url'), 
                null,
                function (result) {
                    switch (result.action) {
                        case 'refresh':
                            document.location.reload();
                        break;
                    }
                }
            );
        }
        
        return false;
    });
});
SCRIPT;
}

class Widget_Metas_Category_List_CountOrdered extends \Widget\Base\Metas
{
    public function execute()
    {
        $this->db->fetchAll(
            $this->select()
                ->where('type = ?', 'category')
                ->order('table.metas.count', \Typecho\Db::SORT_DESC),
            [$this, 'push'],
        );
    }
}

function themeConfig($form)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点 LOGO 地址'),
        _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO')
    );
    $form->addInput($logoUrl);

    $sidebarBlock = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'sidebarBlock',
        [
            'ShowPages'          => _t('显示独立页面链接'),
        ],
        ['ShowPages'],
        _t('侧边栏显示')
    );
    $form->addInput($sidebarBlock->multiMode());

    $sidebarNavLinks = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarNavLinks',
        NULL,
        '', 
        '侧边栏链接', 
        '一行一个，格式："title,href"，例如："归档,/archives"'
    );
    $form->addInput($sidebarNavLinks);

    $sidebarSocialLinks = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarSocialLinks',
        NULL,
        '', 
        '社交网络链接', 
        implode('；', [
            '一行一个，格式："icon,title,href"，例如："github,GitHub,https://github.com"',
            'icon 取值参考：https://fontawesome.com/icons?d=gallery&m=free'
        ])
    );
    $form->addInput($sidebarSocialLinks);

    $licenseIcons = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'licenseIcons',
        [
            'cc'             => _t('Creative Commons'),
            'cc_by'          => _t('署名'),
            'cc_nc'          => _t('非商业性使用'),
            'cc_sa'          => _t('相同方式共享'),
            'cc_nd'          => _t('禁止演绎'),
        ],
        ['cc', 'cc_by'],
        _t('文章许可协议图标')
    );
    $form->addInput($licenseIcons->multiMode());

    $defaultLicenseHtml = <<<EOF
除非另有说明，本页面上的内容采用
<a href="https://creativecommons.org/licenses/by-nc/4.0/deed.zh-hans" target="_blank" rel="noopener">
  知识共享 (Creative Commons) 署名—非商业性使用 4.0 公共许可协议 (CC BY-NC 4.0)
</a>
进行许可。
EOF;
    $licenseHtml = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'licenseHtml',
        NULL,
        $defaultLicenseHtml, 
        '文章许可协议说明', 
        '支持 HTML 标签',
    );
    $form->addInput($licenseHtml);

    $form->addItem(new Ueno_Form_WriteStaticCSS());

    $dynamicStyleSlug = new \Typecho\Widget\Helper\Form\Element\Text(
        'dynamicStyleSlug',
        null,
        null,
        _t('动态渲染 CSS 的独立页面 slug'),
        _t('您需要创建一个“自定义模板”为“动态渲染 CSS”的独立页面，然后将这个独立页面的 slug 填入。填写该字段后主题将从这个页面加载 CSS 样式，一般用于开发调试。若不填写该字段（默认情况），会加载主题目录下的 style.css')
    );
    $form->addInput($dynamicStyleSlug);

    $footerHtml = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'footerHtml',
        null,
        '', 
        '页脚 HTML', 
    );
    $form->addInput($footerHtml);
}

function themeFields($layout)
{
    $coverUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'coverUrl',
        null,
        null,
        _t('封面图 URL')
    );
    $layout->addItem($coverUrl);

    $jsonData = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'jsonData',
        null,
        null,
        _t('JSON 数据')
    );
    $layout->addItem($jsonData);

    $jsonData->input->style = "font-family: monospace; width: 100%; height: 160px";

    $disableToc = new \Typecho\Widget\Helper\Form\Element\Radio(
        'disableToc',
        [
            '0' => _t('显示目录'),
            '1' => _t('隐藏目录'),
        ],
        '0',
        _t('目录')
    );
    $layout->addItem($disableToc);
}

function getSidebarNavLinks()
{
    $sidebarNavLinks = \Typecho\Widget::widget('Widget_Options')->sidebarNavLinks;
    if (empty($sidebarNavLinks)) {
        return [];
    }

    $sidebarNavLinks = explode("\n", trim($sidebarNavLinks));
    $sidebarNavLinks = array_map(function ($item) {
        $item = explode(',', $item);
        return [
            'title' => $item[0],
            'href' => $item[1],
            'isExternal' => strpos($item[1], 'http') === 0,
        ];
    }, $sidebarNavLinks);
    return $sidebarNavLinks;
}

function getSidebarSocialLinks()
{
    $sidebarSocialLinks = \Typecho\Widget::widget('Widget_Options')->sidebarSocialLinks;
    if (empty($sidebarSocialLinks)) {
        return [];
    }

    $sidebarSocialLinks = explode("\n", trim($sidebarSocialLinks));
    $sidebarSocialLinks = array_map(function ($item) {
        $item = explode(',', $item);
        return [
            'icon' => $item[0],
            'title' => $item[1],
            'href' => $item[2],
        ];
    }, $sidebarSocialLinks);
    return $sidebarSocialLinks;
}

function getPagePermalink($slug) {
    \Widget\Options::alloc()->to($options);

    return \Typecho\Common::url(
        \Typecho\Router::url('page', ['slug' => $slug]),
        $options->index
    );
}

function getUsingIconList() {
    \Widget\Options::alloc()->to($options);

    $icons = [];

    foreach (getSidebarSocialLinks() as $link) {
        $icons[] = $link['icon'];
    }

    if (!empty($options->licenseIcons)) {
        foreach ($options->licenseIcons as $icon) {
            $icons[] = $icon;
        }
    }

    return array_unique($icons);
}

function readTyplogIconCss() {
    $css = file_get_contents(__DIR__ . '/css/typlog-icon.css');
    preg_match_all('/\.icon-([a-zA-Z0-9_-]+)\s*\{([^\}]+)\}/', $css, $matches, PREG_SET_ORDER);

    $result = [];
    foreach ($matches as $match) {
        $result[$match[1]] = $match[0];
    }
    return $result;
}

function stripCssWhitespace($content) {
    // remove leading & trailing whitespace
    $content = preg_replace('/^\s*/m', '', $content);
    $content = preg_replace('/\s*$/m', '', $content);

    // replace newlines with a single space
    $content = preg_replace('/\s+/', ' ', $content);

    // remove whitespace around meta characters
    // inspired by stackoverflow.com/questions/15195750/minify-compress-css-with-regex
    $content = preg_replace('/\s*([\*$~^|]?+=|[{};,>~]|!important\b)\s*/', '$1', $content);
    $content = preg_replace('/([\[(:>\+])\s+/', '$1', $content);
    $content = preg_replace('/\s+([\]\)>\+])/', '$1', $content);
    $content = preg_replace('/\s+(:)(?![^\}]*\{)/', '$1', $content);

    // whitespace around + and - can only be stripped inside some pseudo-
    // classes, like `:nth-child(3+2n)`
    // not in things like `calc(3px + 2px)`, shorthands like `3px -2px`, or
    // selectors like `div.weird- p`
    $pseudos = array('nth-child', 'nth-last-child', 'nth-last-of-type', 'nth-of-type');
    $content = preg_replace('/:(' . implode('|', $pseudos) . ')\(\s*([+-]?)\s*(.+?)\s*([+-]?)\s*(.*?)\s*\)/', ':$1($2$3$4$5)', $content);

    // remove semicolon/whitespace followed by closing bracket
    $content = str_replace(';}', '}', $content);

    return trim($content);
}

function renderStylesheet() {
    $usingIcons = getUsingIconList();
    $iconCssMap = readTyplogIconCss();

    $styles = [
        'typlog.css',
        'yue.css',
        'vars.css',
        'layout.css',
        'list-item.css',
        'comments.css',
        'entry/layout.css',
        'entry/tags.css',
        'entry/license.css',
        'entry/page-nav.css',
        'override.css',
    ];

    ob_start();

    foreach ($styles as $style) {
        echo file_get_contents(__DIR__ . '/css/' . $style), "\n";
    }

    foreach ($usingIcons as $icon) {
        if (isset($iconCssMap[$icon])) {
            echo $iconCssMap[$icon], "\n";
        }
    }

    $css = ob_get_clean();
    return stripCssWhitespace($css);
}

function writeStaticStylesheet() {
    $css = renderStylesheet();

    return @file_put_contents(__DIR__ . '/style.css', $css);
}

function handleAdminAction() {
    \Widget\User::alloc()->to($user);
    if (!$user->logged || !$user->pass('administrator', true)) {
        return;
    }
    
    $request = \Typecho\Request::getInstance();
    \Widget\Notice::alloc()->to($notice);

    if ($request->isPost() && $request->is('ueno_action=write-static-css')) {
        if (writeStaticStylesheet()) {
            $notice->set('静态 CSS 文件写入成功', 'success');
        } else {
            $notice->set('静态 CSS 文件写入失败', 'error');
        }

        ob_clean();

        header('Content-Type: application/json');
        echo json_encode([
            'action' => 'refresh',
        ]);

        exit();
    }

    if ($request->isGet() && $request->is('ueno_action=preview-static-css')) {
        ob_clean();
        header('Content-Type: text/css; charset=utf-8');
        header('Content-Disposition: attachment; filename="style.css"');

        echo renderStylesheet();

        exit();
    }
}

function getStaticCSSVersion() {
    $modTime = 0;
    if (@file_exists(__DIR__ . '/style.css')) {
        $modTime = @filemtime(__DIR__ . '/style.css');
    }

    $version = '';

    if ($modTime) {
        $version .= $modTime;
    }

    return htmlspecialchars($version);
}

class RecentModifiedPost extends \Widget\Base\Contents
{
    /**
     * 执行函数
     *
     * @throws \Typecho\Db\Exception
     */
    public function execute()
    {
        $this->parameter->setDefault(['pageSize' => $this->options->postsListSize]);

        $this->db->fetchAll($this->select()
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created < ?', $this->options->time)
            ->where('table.contents.type IN ?', ['post', 'page'])
            ->order('table.contents.modified', \Typecho\Db::SORT_DESC)
            ->limit($this->parameter->pageSize), [$this, 'push']);
    }
}

function getSiteLastUpdateTime() {
    RecentModifiedPost::alloc('pageSize=1')->to($recentPosts);
    if ($recentPosts->have()) {
        $recentPosts->next();
        return $recentPosts->modified;
    } else {
        return null;
    }
}

function getArchiveJsonData($widget) {
    $json = $widget->fields->jsonData;
    if (empty($json)) {
        return null;
    }

    return json_decode($json, true);
}

function _printToc($toc, $index = 0)
{
    if (!isset($toc[$index]))
        return;

    if (empty($toc[$index]['children']))
        return;
?>
<ul>
<?php 
foreach ($toc[$index]['children'] as $k): 
$item = $toc[$k];
?>
<li>
<?php if (isset($item['id'])): ?>
<a href="#<?php echo $item['id']; ?>">
    <span><?php echo $item['title']; ?></span>
</a>
<?php 
endif;
_printToc($toc, $k);
?>
</li>
<?php endforeach; ?>
</ul>
<?php
}

function printContentWithToc($content)
{
    $toc = array(
        0 => array(
            'parent' => null, 
            'children' => array(), 
        ),
    );

    $tocNumStack = array();

    $content = preg_replace_callback(
        '/<h([2-6])(|\s[^>]*)>(.*?)<\/h\1>/i', 
        function ($matches) use(&$toc, &$tocNumStack) {
            $tocIndex = count($toc) - 1;
            $depthCounter = count($tocNumStack);
            
            $depth = intval($matches[1]) - 1;
            $title = trim(strip_tags($matches[3]));

            if ($depthCounter < $depth)
            {
                for (; $depthCounter < $depth - 1; $depthCounter++)
                {
                    $newIndex = count($toc);
                    $toc[] = array(
                        'parent' => $tocIndex,
                        'children' => array(),
                    );
                    $toc[$tocIndex]['children'][] = $newIndex;
                    $tocIndex = $newIndex;
                    $tocNumStack[] = 1;
                }
                if (count($tocNumStack) < $depth)
                {
                    $tocNumStack[] = 0;
                }
            }
            else
            {
                array_splice($tocNumStack, $depth);
                for (; $depthCounter >= $depth; $depthCounter--)
                {
                    $tocIndex = $toc[$tocIndex]['parent'];
                }
            }

            $newIndex = count($toc);
            $tocNumStack[$depth - 1]++;
            $id = preg_replace(
                '/\s+/', 
                '-', 
                implode('-', $tocNumStack) . '-' . $title
            );
            $toc[] = array(
                'parent' => $tocIndex,
                'children' => array(),
                'id' => $id,
                'title' => $title,
                'index' => $tocNumStack[$depth - 1],
            );
            $toc[$tocIndex]['children'][] = $newIndex;
            $tocIndex = $newIndex;
            
            return "<h{$matches[1]} id=\"{$id}\"{$matches[2]}>{$matches[3]}</h{$matches[1]}>";
        },
        $content
    );

    ob_start();
    echo '<div class="toc-container"><div class="toc"><div class="toc-title">文章目录</div>';
    _printToc($toc);
    echo '</div></div>';
    $toc = ob_get_clean();

    $tocPos = strpos($content, '<!--toc-->');
    if ($tocPos === false) {
        $tocPos = strpos($content, '<!--more-->');
    }
    if ($tocPos !== false) {
        $content = substr_replace($content, $toc, $tocPos, 0);
    } else {
        $content = $toc . $content;
    }

    echo $content;
}

handleAdminAction();
