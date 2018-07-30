'use strict';

/**
 * Initialises the table of contents editing.
 */
function contentInitTocEdit() {
    var fieldPrefix;
    var contentTocChangedSelection;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    contentTocChangedSelection = function() {
        jQuery('#' + fieldPrefix + 'includeHeadingLevel').parents('.form-group').toggleClass('hidden', parseInt(jQuery('#' + fieldPrefix + 'includeHeading').val()) < 2);
        jQuery('#' + fieldPrefix + 'includeSubpageLevel').parents('.form-group').toggleClass('hidden', parseInt(jQuery('#' + fieldPrefix + 'includeSubpage').val()) < 2);
        jQuery('#' + fieldPrefix + 'includeSelf').parents('.form-group').toggleClass('hidden', '' == jQuery('#' + fieldPrefix + 'pageId').val());
    };
    jQuery('#' + fieldPrefix + 'includeHeading, #' + fieldPrefix + 'includeSubpage, #' + fieldPrefix + 'pageId').change(contentTocChangedSelection);
    contentTocChangedSelection();
}