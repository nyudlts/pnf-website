---
title: '53. For web-based features that call for a dynamic user experience within a single webpage—e.g. pop-ups, annotations, data visualizations, maps—consider designs that pre-load all data when the page initially loads in the browser.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

In order to improve the likelihood that content published to the web will be able to be captured via web archiving methods, developers could preload any content that would otherwise depend on user interactions. For example, rather than repeatedly making small API calls as the user interacts with a feature, if the dataset that supports the feature is small enough, load the data as a JSON file when the page loads so that further server calls are not necessary.

_This guidelines describes another approach:_  

1. _[Consider a “progressive enhancement” design to support a scriptless environment](/guidelines/50-progressive-enhancement-design)_

[example]
[_A Mid-Republican House from Gabii_](https://doi.org/10.3998/mpub.9231782?target=_blank) on the Fulcrum platform features an interactive 3D visualization of an archaeological dig. The feature allows a user to navigate the visualization and use it to jump to relevant points within the EPUB text. Due to customizations in the Fulcrum web interface to support this feature, the exported version of the EPUB does not include this interactivity. Web archiving is likely the best approach to capture and archive the experience in this case, but many interactive visualizations repeatedly call a live server as the user interacts with it in order to retrieve data related to the new view. These kinds of features are typically not well supported by web crawlers, which cannot easily determine all of the permutations of user interactions within the visualization and then request each possible view of the data from the server to add to the archived copy. In the case of the _Gabii_ project, however, the visualization was successfully archived using a web crawling approach. The Fulcrum team had selected the WebGL technology for the visualization. This preloads all of the visualization data into the browser at the time the page is loaded. When a user interacts with the visualization, the site does not need to retrieve new data from the server to present a new view. This meant the feature could be archived using a web crawler tool in a form that preserved the interactive functionality.
[/example]
