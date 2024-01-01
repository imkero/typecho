<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

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
}

function getSidebarNavLinks()
{
    $sidebarNavLinks = \Typecho\Widget::widget('Widget_Options')->sidebarNavLinks;
    if (empty($sidebarNavLinks)) {
        return [];
    }

    $sidebarNavLinks = explode("\n", $sidebarNavLinks);
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

    $sidebarSocialLinks = explode("\n", $sidebarSocialLinks);
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
