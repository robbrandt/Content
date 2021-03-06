{if $displayMode=='inline'}
<dl class="content-video content-youtube">
    <dt>
	{if $videoMode=='HTML5'}
        <iframe width="{$width}" height="{$height}" src="https://www.youtube.com/embed/{$videoId}?feature=player_detailpage&amp;rel={$showRelated}&amp;autoplay={$autoplay}" frameborder="0" allowfullscreen></iframe>
	{else}{*Legacy Flash embed*}
		<object type="application/x-shockwave-flash" style="width:{$width}px; height:{$height}px" data="http://www.youtube.com/v/{$videoId}">
            <param name="movie" value="http://www.youtube.com/v/{$videoId}" />
        </object>
	{/if}
    </dt>
    <dd>{$text|safetext}&nbsp;|&nbsp;<a href="http://www.youtube.com/v/{$videoId}&amp;rel={$showRelated}&amp;autoplay={$autoplay}">YouTube.com</a></dd>
</dl>
{else}
{pageaddvar name="javascript" value="prototype"}
{pageaddvar name="javascript" value="modules/Content/lib/vendor/lightwindow/javascript/lightwindow.js"}
{pageaddvar name="stylesheet" value="modules/Content/lib/vendor/lightwindow/css/lightwindow.css"}

{* Not allowed in allow_url_fopen=0 case so disabled
{assign var="image" value="http://img.youtube.com/vi/$videoId/default.jpg"}
{assign var="imageSize" value=$image|getimagesize}
*}

<dl class="content-video content-youtube">
    <dt>
        <a title="{$text|safetext}" href="http://www.youtube.com/v/{$videoId}&amp;rel={$showRelated}&amp;autoplay={$autoplay}" class="lightwindow page-options" params="lightwindow_width={$width},lightwindow_height={$height},lightwindow_loading_animation=false"><img src="http://img.youtube.com/vi/{$videoId}/default.jpg" alt="{$text|safetext}" /></a>
    </dt>
    <dd>{$text|safetext}</dd>
    <dd><a title="{$text|safetext}" href="http://www.youtube.com/v/{$videoId}&amp;rel={$showRelated}&amp;autoplay={$autoplay}" class="play-icon lightwindow page-options" params="lightwindow_width={$width},lightwindow_height={$height},lightwindow_loading_animation=false">{gt text="Play Video"}</a></dd>
</dl>
{/if}
