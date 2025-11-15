---
title: '50. For highly dynamic websites that use a lot of JavaScript to interact with the server in the background, consider a “progressive enhancement” design approach.'
taxonomy:
    tag:
        - 'publishing platforms'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Many modern websites depend on JavaScript to load data from the server as the user interacts with the site creating a dynamic experience. This can make it difficult for a web crawler to automatically create a functional copy of a web page since it may not be able to predict all user behaviors that pull new content from the server. Some web developers design websites using a “progressive enhancement” approach in which a baseline of functionality is supported for a variety of environments, including those with scripts disabled. Where this approach is used, the version of the site presented to the user will change if they choose to disable, or cannot support, JavaScript in their environment. They will instead see a scriptless version of the site that presents the core intellectual components of the page in a more static form. If this functionality exists or can be easily supported, it can serve as an alternative way to capture pages using web archiving in cases where the full dynamic version cannot be crawled automatically.

_This guideline describes an alternative way to manage JavaScript-rich features:_  
_53. [For dynamic web page features, favor designs that pre-load data](/guidelines/53-preload-data-in-browser)_

!> Example
A form of this concept can be found in the Fulcrum resource pages. For each type of media resource, there are two ways to access the content: you can either view it in an enhanced media viewer that is embedded in the page or download the file via the download button. For those who can access the enhanced viewers on Fulcrum, they provide additional functionality such as speeding up and slowing down video, zooming into images, and viewing highlighted audio transcripts while listening to audio. If those enhanced viewers aren’t available to the user, the resource pages also include a download button so that the user can copy the file to their own machine to view it. This is helpful to web archiving approaches, since this approach may fail to capture all of the complexity of the enhanced media viewers, but it will almost certainly be able to copy the downloaded file if it is linked from the same page as the viewer.
!@