{if $useGraphics}
<p><ul id="content-breadcrumbs">
{if $includeHome}<li><a href="{homepage}">{img src=folder_home.png modname=core set=icons/extrasmall __title="Home" __alt="Home"} {gt text='Home'}</a></li>{/if}
{foreach from=$path item="page"}
    <li>{if $page.id != $thispage}<a href="{modurl modname='Content' type='user' func='view' pid=$page.id}">{/if}{$page.title|safetext}{if $page.id != $thispage}</a>{/if}</li>
{/foreach}
</ul></p>
{else}
<p>
{if $includeHome}{img src=folder_home.png modname=core set=icons/extrasmall __title="Home" __alt="Home" } <a href="{homepage}">{gt text='Home'}</a> {$delimiter} {/if}
{assign var='notfirst' value='0'}
{foreach from=$path item='page'}
{if $notfirst} {$delimiter} {/if}
{if $page.id != $thispage}<a href="{modurl modname='Content' type='user' func='view' pid=$page.id}">{/if}{$page.title|safetext}{if $page.id != $thispage}</a>{/if}
{assign var='notfirst' value='1'}
{/foreach}
</p>
{/if}
{* For more graphical enhancement, see:
- http://css-tricks.com/triangle-breadcrumbs/
- http://veerle-v2.duoh.com/blog/comments/simple_scalable_css_based_breadcrumbs/ *}