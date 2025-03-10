<?php
/*
 * Меню админ-панели
 * Admin panel menu
 */

return [
    [
        'url'   => getUrlByName('admin'),
        'name'  => Translate::get('admin'),
        'icon'  => 'bi bi-shield-exclamation',
        'item'  => 'admin',
    ], [
        'url'   => getUrlByName('admin.users'),
        'name'  => Translate::get('users'),
        'icon'  => 'bi bi-people',
        'item'  => 'users',
    ], [
        'url'   => getUrlByName('admin.reports'),
        'name'  => Translate::get('reports'),
        'icon'  => 'bi bi-flag',
        'item'  => 'reports',
    ], [
        'url'   => getUrlByName('admin.audits'),
        'name'  => Translate::get('audits'),
        'icon'  => 'bi bi-exclamation-diamond',
        'item'  => 'audits',
    ], [
        'url'   => getUrlByName('admin.topics'),
        'name'  => Translate::get('topics'),
        'icon'  => 'bi bi-columns-gap',
        'item'  => 'topic',
    ], [
        'url'   => getUrlByName('admin.blogs'),
        'name'  => Translate::get('blogs'),
        'icon'  => 'bi bi-journal-text',
        'item'  => 'blog',
    ], [
        'url'   => getUrlByName('admin.invitations'),
        'name'  => Translate::get('invites'),
        'icon'  => 'bi bi-person-plus',
        'item'  => 'invitations',
    ], [
        'url'   => getUrlByName('admin.posts'),
        'name'  => Translate::get('posts'),
        'icon'  => 'bi bi-journal-text',
        'item'  => 'posts',
    ], [
        'url'   => getUrlByName('admin.comments'),
        'name'  => Translate::get('comments'),
        'icon'  => 'bi bi-chat-dots',
        'item'  => 'comments-n',
    ], [
        'url'   => getUrlByName('admin.answers'),
        'name'  => Translate::get('answers'),
        'icon'  => 'bi bi-chat-left-text',
        'item'  => 'answers-n',
    ], [
        'url'   => getUrlByName('admin.badges'),
        'name'  => Translate::get('badges'),
        'icon'  => 'bi bi-award',
        'item'  => 'badges',
    ], [
        'url'   => getUrlByName('admin.webs'),
        'name'  => Translate::get('domains'),
        'icon'  => 'bi bi-link-45deg',
        'item'  => 'domains',
    ], [
        'url'   => getUrlByName('admin.words'),
        'name'  => Translate::get('stop words'),
        'icon'  => 'bi bi-badge-ad',
        'item'  => 'words',
    ], [
        'url'   => getUrlByName('admin.tools'),
        'name'  => Translate::get('tools'),
        'icon'  => 'bi bi-tools',
        'item'  => 'tools',
    ],
];
