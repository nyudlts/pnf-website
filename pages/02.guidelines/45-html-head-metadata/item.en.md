---
title: '45. Embed structured bibliographic metadata in the <head> of a web-based publication.'
taxonomy:
    tag:
        - 'publishing platforms'
        - 'web-based publications'
publish_date: '02-09-2021 10:36'
---

This can help facilitate a fully automated web harvest of content in situations where an export is not a feasible approach. Bibliographic metadata is a vital component of a publication preservation package. As with other metadata it’s best to use a broadly adopted standard such as Google Scholar, Dublin Core, or PRISM. Cover the core bibliographic information to make the publication findable, and be consistent. An expression of the material’s license, for example, through `<link rel="license" href=...>`, is valuable since this can support an archive’s understanding of whether the material can be preserved and how it can be reused. Note that HTTP Link headers can also be used to convey some metadata and can be applied to the HTTP Response of both HTML and non-HTML web resources. An approach to this is described on [signposting.org](https://signposting.org/?data-versionurl=https://web.archive.org/web/20210712222345/https://signposting.org/&data-versiondate=2021-09-01&target=_blank).

_These guidelines may also be relevant when generating bibliographic metadata:_  
_21. [Provide bibliographic metadata with exported publications](/guidelines/21-structured-bibliographic-metadata)_  
_30. [Bibliographic metadata in the context of EPUBs](/guidelines/30-epub-bibliographic-metadata)_  
_40. [The license for external resources can be expressed in HTML](/guidelines/40-external-web-content-rights)_

[example]
The enhanced journal [_Technology, Mind & Behavior_](https://tmb.apaopen.org/?target=_blank) (_TMB_) is hosted on PubPub. Publishers can configure articles on PubPub to display the full article metadata in the `<head>` section of the web page HTML. If you inspect the HTML code of a PubPub-based _TMB_ article, you will see that the `<head>` element of the document at the top of the document includes bibliographic metadata in the `<meta>` tags and has implemented several standards. One is `citation_`, which is used by Google Scholar to create search records, and another is `dc` which stands for Dublin Core, a widely used descriptive metadata standard. Including metadata in these formats supports archiving that is performed by automatic harvesting. It allows harvest tools to extract accurate descriptive metadata from the webpage of the article. Note that in the case of PubPub, the license is not in the `<head>` section, which would be ideal, but is expressed at the bottom of the page using the Creative Commons relationship. The anchor tag for that license has the format `<a rel="license">` which indicates this connects to the license for the page.
[/example]