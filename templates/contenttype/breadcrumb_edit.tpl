<div class='z-formrow' id='selectIncludeSelf'>
    {formlabel for='includeSelf' __text='Include self as last breadcrumb'}
    {formcheckbox id='includeSelf' group='data'}
</div>

<div class='z-formrow' id='selectIncludeHome'>
    {formlabel for='includeHome' __text='Include home as first breadcrumb'}
    {formcheckbox id='includeHome' group='data'}
</div>

<div class='z-formrow' id='selectTranslateTitles'>
    {formlabel for='translateTitles' __text='Show translated titles'}
    {formcheckbox id='translateTitles' group='data'}
</div>

<div class='z-formrow' id='selectUseGraphics'>
    {formlabel for='useGraphics' __text='Use graphical breadcrumb trail (default is plain text)'}
    {formcheckbox id='useGraphics' group='data'}
</div>

<div class='z-formrow'>
    {formlabel for='delimiter' __text='Delimiter'}
    {formtextinput id='delimiter' maxLength='255' group='data'}
</div>

