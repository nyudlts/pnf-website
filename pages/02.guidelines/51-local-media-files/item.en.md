---
title: '51. When embedding video and other media in web-based publications, host the media files local to the website using standard HTML tags rather than depending on third party services for streaming.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Linking to media that is hosted on YouTube or Vimeo is a threat to platform and content longevity, especially for media that is owned or managed by third parties. In order to mitigate against future link rot and the general instability of archiving streamed content, where appropriate (technically and legally), host a local copy of any media assets and embed it in the web page using standard HTML5 media tags. In order to keep the overall size of embedded media manageable for access and for the purpose of web archiving, it may be advantageous to embed lower quality copies of the media and link to higher resolution versions via persistent links such as DOIs.

_See also:_  

1. _[Start discussions about multimedia features early](/guidelines/12-discuss-media-assets-early)_  
2. _[Avoid depending on externally hosted web services](/guidelines/14-avoid-external-services)_

[example]
Created as part of the Brown University Digital Publications program and published by University of Virginia Press, _Furnace and Fugue_ by Tara Nummedal includes a variety of audio and video features. Instead of using a third party service to stream the audio and video, these files are stored local to the website in a subfolder for assets associated with the project. They are embedded using HTML `<video>` and `<audio>` tags. An example of this can be seen on <a href="https://doi.org/10.26300/bdp.ff.nummedal-bilak" target="_blank">the Essays "Interplay" page</a>, where the site designers opted to use a `<video>` element rather than utilize a service such as Vimeo and YouTube. Hosting these videos local to the site and embedding them using simple HTML tags reduces the chance the audio or video will be lost over time and improves the preservability.
[/example]