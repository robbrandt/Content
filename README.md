# Deprecated

This module has been deprecated as part of [Zikula 4 slimming diet](https://github.com/zikula/core/blob/main/ZIKULA-4.0.md).

# Content module for the Zikula Application Framework

[![](https://github.com/zikula-modules/Content/workflows/Generate%20module/badge.svg)](https://github.com/zikula-modules/Content/actions?query=workflow%3A"Generate+module")
[![](https://github.com/zikula-modules/Content/workflows/Test%20module/badge.svg)](https://github.com/zikula-modules/Content/actions?query=workflow%3A"Test+module")

## Documentation

1. [Introduction](#introduction)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Upgrading](#upgrading)
5. [How to link pages](#how-to-link-pages)
6. [Integration with menu module](#integration-with-menu-module)
7. [Implementing custom content types](#implementing-custom-content-types)
8. [Changelog](#changelog)
9. [TODO](#todo)
10. [Questions, bugs and contributing](#questions-bugs-and-contributing)

## Introduction

Content is a hierarchical page editing module for Zikula 3. With it you can insert and edit various content items, such as HTML texts, videos, maps and much more. Also content of other modules and blocks can be shown inside a Content page. 

Each page can arrange arbitrary content elements using Bootstrap grids.

It also features additional functionality, like translating content and tracking changes between different versions.

## Requirements

The `main` branch of this module is intended for being used with Zikula 3.1.
For Zikula 3.0.x and 2.0.x look at [releases](https://github.com/zikula-modules/Content/releases/).

## Installation

The Content module is installed like this:

1. Download the [latest release](https://github.com/zikula-modules/Content/releases/latest).
2. Copy the content of `extensions/` into the `extensions/` directory of your Zikula installation. Afterwards you should a folder named `extensions/Zikula/ContentModule/`.
3. Initialize and activate ZikulaContentModule in the extensions administration.

## Upgrading

<details>
<summary>

### Upgrade to Content 5.2.0 (Zikula 2.x to 3.x)

</summary>
<div>

1. Ensure you have Zikula 2.x with Content 5.1.0 running (download from the [this release](https://github.com/zikula-modules/Content/releases/tag/5.1.0)).
2. Upgrade Zikula core to 3.x.
3. Delete the `modules/Zikula/ContentModule/` directory entirely.
4. Copy the content of `extensions/` into the `extensions/` directory of your Zikula installation. Afterwards you should a folder named `extensions/Zikula/ContentModule/`.
5. In `/.env.local` set `APP_DEBUG=1`.
6. **Create a backup of your database!**
7. Update ZikulaContentModule in the extensions administration.
8. In `/.env.local` set `APP_DEBUG=0`.

In case something goes wrong:

1. Restore your database dump.
2. Report your problem in the issue tracker at <https://github.com/zikula-modules/Content/issues> - in case you got an exception please post the complete stack trace.
3. Add the patch or follow the advice you got.
4. Update ZikulaContentModule in the extensions administration again.

</div>
</details>
<details>
<summary>

### Upgrade to Content 5.0.0 - 5.1.0 (Zikula 1.5.x to 2.x)

</summary>
<div>

1. Ensure you have Zikula 1.5.x with Content 4.2.2 running (download from the [1.3-legacy branch](https://github.com/zikula-modules/Content/tree/1.3-legacy)).
2. Upgrade Zikula core to 2.x.
3. Delete the `modules/Content/` directory entirely.
4. Copy the content of `modules/` into the `modules/` directory of your Zikula installation. Afterwards you should a folder named `modules/Zikula/ContentModule/`.
5. In `/app/config/custom_parameters.yml` set `debug: true`.
6. **Create a backup of your database!**
7. Update ZikulaContentModule in the extensions administration.
8. In `/app/config/custom_parameters.yml` set `debug: false`.

In case something goes wrong:

1. Restore your database dump.
2. Report your problem in the issue tracker at <https://github.com/zikula-modules/Content/issues> - in case you got an exception please post the complete stack trace.
3. Add the patch or follow the advice you got.
4. Update ZikulaContentModule in the extensions administration again.

The upgrade currently migrates the following data:

- Page data and pages hierarchy tree
- Primary page category assignments
- Page translations
- Page content
- Page content translations

Not migrated are:

- Additional page category assignments
- Page histories (older revisions)
- Content provided by content types from 3rd party modules (instead at least _HTML_ elements are added containing a note about which type of element has been there before)
- Page layout arrangement data

Work to do after upgrade:

1. Rearrange your content elements re-creating your page layouts.
2. Review and update manual/static links to your pages.
3. Upgrade 3rd party modules providing additional content types you need.

</div>
</details>

## How to link pages

When you statically refer to pages in some templates you will see one problem: linking to the details of a page requires the slug which may be different per language and could be changed later which will break your link. For this a Twig function is provided to get the slug from the page id. For example:

```
<a href="{{ path('zikulacontentmodule_page_display', {slug: zikulacontentmodule_getSlug(2)}) }}" title="Test page">Test page</a>
```

## Integration with menu module

Content offers a dedicated menu block. But it can also be combined with menus from the menu module which is provided by Zikula core. You can add nodes with a placeholder title like `ContentPages_123` whereby `123` is the ID of a certain page. When displaying the menu this placeholder will be replaced by the corresponding pages sub tree. Note that only those pages are shown which are currently active, have the "in menu" flag enabled and visible to the current user.

By default there are unlimited levels added to a menu. You can limit this by adding an `extras` option to the menu entry with value `{"levels":2}` where `2` is the desired amount of levels.

## Implementing custom content types

In content each page consists of several content items. Each content item uses a certain content type. Interestingly, other modules can provide additional content types, so for example a calendar module can offer a content type for displaying a list of events for a specific category or the details of a single event. For each content type a corresponding class needs to be implemented which cares about displaying, editing and translating the managed data.

- A content type class names should be suffixed by `Type` and located in the `ModuleRoot/ContentType/` directory. This is not mandatory but a recommended convention.
- Content type classes must implement `Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface`.
- Content type classes may extend `Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType` for convenience.
- Content type classes must define a Symfony form type class to allow editing of their data fields if this is needed. This form type class should extend `Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType` for convenience.
   Otherwise the `getEditFormClass` method must return `null`.
- The convention for template files of a content type with name `foo` is as follows:
  - Display: `@AcmeFooModule/ContentType/FooView.html.twig`
  - Edit subform: `@AcmeFooModule/ContentType/FooEdit.html.twig`
  - In the edit sub form template, **do not** render the `form_start(form)` or `form_end(form)` tags.

## Changelog

<details>
<summary>

### Version 5.3.0

</summary>
<div>

New features:

- None yet.

Bugfixes:

- Fixed combination of owner permission, private mode and only own flag.
- Fixed installation error in `ExampleDataHelper` (#571).

</div>
</details>
<details>
<summary>

### Version 5.2.0

</summary>
<div>

Structural changes:

- Upgrades for Zikula 3.0.x.

New features:

- Tab titles of tab navigation elements can be translated.

Bugfixes:

- Fixed upgrade issue (#338).
- Prevent log entry creation from other modules.
- Fixed sporadic scrolling problem in editing modal.
- Fixed wrong combination of translatable tree slugs (#309).
- Fixed tree selection form type with duplicate labels.

</div>
</details>
<details>
<summary>

### Version 5.1.0

</summary>
<div>

New features:

- Replaced old management palette by a selection modal dialog (#303).
- Dynamically toggle z-index of notification container to avoid confusing UI behaviour (non-clickable dropdown menus).
- Allow selection of multiple scopes for a content item (#325).
- Allow restricting pages to (multiple) scopes, too (#325).
- Added several options to author information plugin (#298).
- Added missing filter hook calls.
- Added new setting for automatic linking of page titles using MultiHook (#335).
- Added MultiHook needle.

Bugfixes:

- Minor fixes for external display (#332).

</div>
</details>
<details>
<summary>

### Version 5.0.3

</summary>
<div>

New features:

- Added new flag for toggling root page in `TableOfContents` plugin.
- Added possibility for private mode so each author only sees his own pages.

Bugfixes:

- Fixed pagination issue related to combination of categories and translatable data.
- Restrict custom loggable listener action to entities from same bundle.
- Fixed possible invalid states for boolean filter in quick navigation.
- Overhauled and improved asset handling on raw pages.
- Fixed exception in extended revert of older revisions.
- Fixed logical issue in `TableOfContents` plugin.
- More solid revertion to old revisions.
- Several minor fixes from generator.

</div>
</details>
<details>
<summary>

### Version 5.0.2

</summary>
<div>

New features:

- Added missing support for query and request arguments to controller content type (#307).
- Use title as fallback for description in finder.

Bugfixes:

- Addition of (minor) safety check for Slideshare, Vimeo and YouTube content types.
- Provide full tree slug during first page translation step for proper step jumps.

</div>
</details>
<details>
<summary>

### Version 5.0.1

</summary>
<div>

Bugfixes:

- Fixed hardcoded specific directory structure for Scribite integration (#300).

</div>
</details>
<details>
<summary>

### Version 5.0.0

</summary>
<div>

Structural changes:

- Entirely rewritten for Zikula 2.0.x using ModuleStudio.

New features:

- New UI for managing and arranging content elements more quickly and easily.
- Replaced old page layout types by a new concept for dynamic page layouts.
- A tree slug handler is utilised for creating hierarchical permalinks/URLs.
- Publication of content types can be restricted using start and/or end dates.
- Publication of content types can be restricted to specific user groups.
- Content types are now collected based on Symfony container using service tags.
- Content types are grouped into different categories.
- Content elements can be moved and copied to other pages.
- Provides plugins for Scribite editors (CKEditor, Quill, Sommernote, TinyMCE).
- Hooks can not only be attached to complete pages, but also to single content items.
- Beside UI hooks and filter hooks also form aware hooks are supported.
- The ModuleFunc content type has been renamed to Controller since it now supports not only modules, but all types of Symfony bundles.
- Google map and route content types support different map types instead of roadmaps only.
- OpenStreetMap content type has been replaced by a much more powerful Leaflet content type.
- Slideshare and vimeo content types fetch additional data from the corresponding APIs.
- Menu block has new options for different navigation types and sub pages handling.
- All blocks showing lists of pages have included detection of currently active page.
- Added owner permission support to allow non-admin users to manage their own pages.
- Custom (and multiple) CSS classes can now be used for pages, page sections and single content elements.
- Permalink settings for removing unwanted URL parts.
- Added no cookie option for YouTube videos.
- Overhauled translation workflow.
- Optional translation support powered by [Yandex.Translate](https://translate.yandex.com/).
- Content items can store additional search texts which is not shown anywhere but only used for searching.

Deprecations:

- Removed the ability to register a page var for breadcrumbs in favour of a dedicated module for this purpose. There is still a Twig function for retrieving or displaying a page hierarchy though.
- Removed the possibility to order sub pages of a specific page by title.
- The JoinPosition content type has been removed because it is not needed anymore.
- The Camtasia content type has been removed.
- The Flickr content type has been removed. A better choice is the Flickr media type in the media module which is going to provide a generic media content type soon (see https://github.com/cmfcmf/MediaModule/issues/2 for reference).
- The FlashMovie content type has been removed. This is better handled by a media module, too.

</div>
</details>

## TODO

- The `ComputerCodeType` needs to be updated for supporting the BBCode and LuMicuLa modules as soon as they have been migrated to Zikula 3. There are @todo markers for that.
- The `YouTubeType` could be enhanced to fetch additional data from API. It should use the `CacheHelper` like `SlideshareType` and `VimeoType`. There is a @todo marker for that.

## Questions, bugs and contributing

If you want to report something or help out with further development of the Content module please refer
to the corresponding GitHub project at <https://github.com/zikula-modules/Content>.
