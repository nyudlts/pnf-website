---
title: '38. If external web content is visually embedded in an EPUB, include URLs in the package metadata.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - EPUB
---

If externally linked web content must be visually embedded in an EPUB, recognize that it is at very high risk for loss. If the content cannot be moved inside the EPUB container using supported features, this material should have an informative caption and be described clearly in the structural metadata within the EPUB. Specifically, the package’s manifest metadata should have an item that: (a) specifies the resource URL (b) lists “remote-resources” as a property, and (c) defines a fallback item. If the embedded web content is not supplied to the preservation service, but can be successfully harvested, this additional metadata could facilitate a preservation workflow to identify and capture these features using an appropriate harvesting tool. If, for example, a visually embedded Google Trends chart no longer displays active content in the future, an archived web page with this chart could be accessed instead. This content should be noted consistently and documented as part of the publication that needs to be preserved. In general, any consistency that makes it easy to automatically identify the visually embedded web-based features within the text increases the chance of designing a scalable workflow to manage it.

_These guidelines may also be relevant to embedding web content in an EPUB:_  

* _[16 - Captions for non-text features add meaningful context](/guidelines/16-create-meaningful-captions)_  
* _[40 - Indicate the license status of resource in the HTML around the object](/guidelines/40-external-web-content-rights)_  
* _[41 - Use HTML iframes with caution](/guidelines/41-caution-using-iframes)_  
* _[42 - Facilitate a local web archive workflow for iframe content](/guidelines/42-web-archive-iframe)_
