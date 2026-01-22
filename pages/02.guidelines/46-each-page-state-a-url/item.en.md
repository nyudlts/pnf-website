---
title: '46. On a website, give each unique page state its own URL.'
taxonomy:
    tag:
        - 'publishing platforms'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

Data driven websites can display different sets of resources from the server at the same URL. If different views of a page share the same URL, however, this means that retrieving a web page from a web archive could have unpredictable results. It is therefore helpful to ensure that, where reasonable, the URL reflects any filters or properties that change what is loaded into the browser from the server via the path or querystring (the part of the URL following the question mark). This allows the different states of a page to be bookmarked, but also makes it possible to utilize a sitemap to express the full range of resources that make up the website. While a sitemap can include API calls that might be used for dynamically generated views, sitemaps are easier to maintain if these views are also reflected in the browser’s address bar.

_This is another guideline about making URLs web archive friendly:_  

1. _[Parameters should not be added to the URL unnecessarily](/guidelines/49-url-parameters-reflect-data)_
