---
title: '57. For web-based features that require perpetual or open ended communication with a live server, consider alternative strategies to provide a representation of this feature to a preservation service.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Some web-based features require communication with a server that is driven by an unpredictable user interaction or utilizes an open-ended number of URLs to retrieve the data to support that feature. These features cannot be exported easily due to their dependence on a live website and cannot be captured well using web archiving, which depends on identifying every unique URL. Examples include: dynamic maps (e.g. Google Maps), full text or faceted search, web forms, data visualizations (e.g. ArcGIS), IIIF image viewers, and streamed content. Some features can be redesigned to remove their dependency on a live server, but if they can’t, publishers will need to consider what can be preserved. There are many strategies for this: for example, create a simpler static version of the feature that incorporates the key features for the purpose of preservation; embed a local copy of a server based resource rather than depend on a third party service; supply code or data for the feature with documentation for re-assembling the functionality; record a video of the interaction as it behaves in the published environment for future playback; or, a combination of these.

_These guidelines offer alternative ways to manage features that depend on a live server:_  
_16. [Captions add important context to non-text features](/guidelines/16-create-meaningful-captions)_  
_53. [Consider web page designs that pre-load all data when the page loads](/guidelines/53-preload-data-in-browser)_  
_63. [Supply raw data, documentation for data visualizations](/guidelines/63-data-visualization-raw-data)_

[example]
In [_Owning My Masters (Mastered)_](https://doi.org/10.3998/mpub.12584348?target=_blank) by A.D Carson, some images are embedded in the EPUB using a IIIF viewer. As a user changes the view on these images by panning or zooming, the tool communicates with a live server to load new image tiles. It is difficult to preserve this interactivity whether via export or web harvesting. To ensure it is possible to view this image in the preserved copy, Fulcrum links this feature to a Resource page on which there is a download button to retrieve a static copy of the image. Here is an example: [This static copy of the image](https://doi.org/10.3998/mpub.12584348.cmp.58?target=_blank) can be easily harvested by a web crawler since the button uses a standard HTML anchor link. Fulcrum also includes this static version of the image in its export package. This ensures viewers can see the full resolution image without needing the IIIF feature to function.
[/example]