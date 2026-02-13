---
title: '39. Avoid using iframes to embed multimedia in EPUBs.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - EPUB
---

Iframe, short for “inline frame,” is an HTML tag that can be used to embed the content from any URL inside an HTML-based document such as an EPUB or webpage. Some publishers may use an iframe to embed things like YouTube videos, or advanced media players into an EPUB. It is more sustainable to use html `<video>` or `<audio>` elements when embedding audio or video. EPUB 3 readers are not required to support iframes. If used, the content may not render in all EPUB 3 readers and is at a high risk of loss through link rot.

_These guidelines are also be relevant to embedding media in EPUBs:_  

* _[12 - Start discussions around multimedia early in the process](/guidelines/12-discuss-media-assets-early)_  
* _[14 - Avoid external dependencies in general](/guidelines/14-avoid-external-services)_  
* _[34 - Opt for core media types when embedding multimedia in an EPUB](/guidelines/34-use-epub-core-media-types)_
