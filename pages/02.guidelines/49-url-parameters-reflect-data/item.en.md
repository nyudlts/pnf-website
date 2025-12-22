---
title: '49. On a website, parameters added to a URL should reflect the data that is loaded from the server and not be added unnecessarily.'
taxonomy:
    tag:
        - 'publishing platforms'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Website crawling and playback of web archives use URLs as unique references—this includes the query parameters (after the “?” and, for some tools, after the “#”). Adding parameters to the URL that do not affect what data is loaded from the server, or simply reflect a default where the page is the same with or without the property, complicates the capture and playback of the web archive and bloats the size of the crawl since every URL is captured as if it is a new page even if the content is identical.

_This guideline is also useful for creating web archive friendly URLs:_  
_46. [Assign each unique page state one, and only one, URL](/guidelines/46-each-page-state-a-url)_

[example]
Language tags, such as `?locale=en` for English, may be appended to the end of the URL to reflect the display language of the publication. If the default language for the publication is the same in the basic URL without the language tag, a web crawler will make a redundant copy by crawling both the basic URL and the URL with the tag. If the publication is available in multiple languages, the language tag could be used for only non-default languages, or every language could include a language tag.
[/example]