
<{if $mLevel == 0}>
    <li data-name="app<{$mm.id}>" class="layui-nav-item">
        <a <{if $mm.src}>lay-href="<{$mm.src|default:'javascript:;'}>"<{else}>href="javascript:;"<{/if}> lay-tips="<{$mm.text}>" lay-direction="2">
            <i class="layui-icon <{$mm.iconSpan|default:'layui-icon-file'}>"></i>
            <cite><{$mm.text}></cite>
        </a>
<{else}>
    <dd data-name="work<{$mm.id}>">
      <a <{if $mm.src}>lay-href="<{$mm.src|default:'javascript:;'}>"<{else}>href="javascript:;"<{/if}>><{$mm.text}></a>
<{/if}>

<{if $mm.children}>
    <dl class="layui-nav-child">
        <{assign var=mLevel value=$mLevel+1}>
        <{foreach from=$mm.children item=mm}>
            <{include file=menuFile.tpl}>
        <{/foreach}>
    </dl>
<{/if}>
<{if $mLevel == 0}>
    </li>
<{else}>
    </dd>
<{/if}>