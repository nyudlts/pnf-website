---
title: '40. If external web content is embedded in a publication, identify the rights and note intention for collecting this content as part of the publication.'
taxonomy:
    tag:
        - rights
        - 'third-party dependencies'
        - 'embedded resources'
        - EPUB
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Some preservation services will not collect web content outside of the agreed upon domain names unless copyright for the content being harvested is clear. If third-party pages and features that are visually embedded in an EPUB or a web-based publication are meant to be preserved, it should be possible to identify which content publishers have the right to collect them so that a web crawler can be configured to include or exclude them. One way to communicate these rights is to express them in the metadata that is supplied to the preservation service. Another option is to apply structured metadata describing the rights status to the HTML. The <a href="https://web.archive.org/web/20220201020148/https://labs.creativecommons.org/2011/ccrel-guide/" data-versionurl="https://web.archive.org/web/20210506215035/https://labs.creativecommons.org/2011/ccrel-guide/" data-versiondate="2021-09-01" target="_blank">Creative Commons REL documentation</a> includes examples of this that cover both page- and object-level licenses. This approach could support automated harvesting decisions at either level. Alternatively, a publisher could supply a list of domain names to include for harvest during the initial preservation workflow configuration.

_These guidelines may also be useful to consider when embedding external web content:_  

* _[25 - Add license information to resource-level metadata](/guidelines/25-license-publication-resources)_  
* _[38 - List the URLs for external web content in the metadata](/guidelines/38-include-external-urls-in-metadata)_  
* _[45 - Embed metadata that includes a license in the `<head>` of a web page](/guidelines/45-html-head-metadata)_  
* _[70 - Consider systematically tagging component that should be excluded for preservation](/guidelines/70-consider-tagging-material-that-should-be-excluded)_
