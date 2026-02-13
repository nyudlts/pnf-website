---
title: '58. For a custom web application built for a single publication, consider encapsulation of features early.'
taxonomy:
    tag:
        - planning
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

If publishers are involved early enough in the development process for a custom web application that is being built for a single publication, they should encourage developers and authors to make choices that avoid external dependencies or to have fallback mechanisms when external dependencies fail. For example, if a connection to Google Maps fails, fall back to a still image and the vector coordinates. Developers can test their site by running it in a virtual environment with no internet connection. If it works, it is not only likely to be easier to preserve, but also much more sustainable and easier for the publisher to maintain.

_These guidelines may be referred to when considering encapsulation:_  

* _[14 - Avoid depending on externally hosted web services](/guidelines/14-avoid-external-services)_  
* _[51 - Embed multimedia locally](/guidelines/51-local-media-files)_  
* _[56 - Avoid embedding map visualizations where a static representation would suffice](/guidelines/56-link-to-dynamic-map)_
