---
title: '42. If external web content is visually embedded in an EPUB via an iframe, reduce risk of total loss of these features by facilitating a local web archiving workflow.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - EPUB
publish_date: '02-09-2021 10:36'
---

Preservation services might not support a workflow that automatically harvests the content of iframes embedded within an EPUB. Even if such harvesting is supported, the quality could vary greatly, and the content might change following publication. If fallback options are not sufficient a more stable approach would be for the publisher to create an archived copy of the web page featured in the iframe. While there are tools such as Webrecorder’s [ArchiveWeb.page](https://ArchiveWeb.page?data-versionurl=https://web.archive.org/web/20250307215217/https://archiveweb.page/&data-versiondate=2025-03-07&target=_blank) that can be run locally by the publisher to perform single page archiving, there are also third party archiving services such as [archive.today](http://archive.is/?target=_blank&data-versionurl=https://web.archive.org/web/20210531102537/https://archive.is/&data-versiondate=2021-05-31) or [Internet Archive’s Save Page Now](https://web.archive.org/save/?target=_blank&data-versionurl=https://web.archive.org/web/20210915131158/https://web.archive.org/save/&data-versiondate=2021-09-15) service that allow you to archive a single page and generate a persistent link for the embedded web content. This link could be included in a descriptive caption under the embedded feature. Publishers should test the outcome of these single page captures as quality can vary depending on the complexity of the website and the harvest method applied.

_These guidelines may also be relevant:_  
_14. [Avoid dependence on externally hosted platforms for core features](/guidelines/14-avoid-external-services)_  
_15. [Plan a strategy for preservation when third party dependencies exist](/guidelines/15-plan-for-third-party-hosted-media)_  
_39. [Avoid the use of iframes to embed multimedia](/guidelines/39-avoid-iframes-in-epubs)_