---
title: '16. Create a meaningful caption for all non-text features embedded in a publication.'
taxonomy:
    tag:
        - 'third-party dependencies'
        - 'embedded resources'
        - EPUB
        - 'web-based publications'
---

Embedded enhanced features, especially those that link to resources outside of the publication or use an unusual format, are at the highest risk of failing in the future. For this reason, a meaningful caption is vital for providing clues to future readers about what they should expect to find in that location in the text, and preferably some means of finding it and accessing it. Ideally, this caption would include a title, source, unique persistent identifier (e.g. DOI, ARK ID, or Handle), and a link to an archived copy if different from the identifier. Though any link could ultimately fail, this information would at least provide clues to where the user might find an archived copy. When creating captions, apply the standards available within the format you are using to support automated parsing. For example, use HTML5 `<figure>` and `<figcaption>` elements. Alt attributes are also widely used to supply a description in case a feature cannot be viewed. In this respect, a meaningful caption may also meet standards for digital accessibility. For a fuller treatment of this topic, see the poster presentation [Embedding Preservability: IFrames in Complex Scholarly Publications](http://hdl.handle.net/2451/74900&target=_blank), presented at the IPRES 2023 conference.

_Where non-text features are supplied as separate publication resources, this guideline may also be relevant:_    

* _[24 - Create metadata for each publication resource](/guidelines/24-metadata-for-resources)_

[example]
On the Fulcrum platform, University of Michigan Press requires authors to create alt text for all images and caption descriptions for media files. <a href="https://press.umich.edu/For-Authors/Author-s-Guide" data-versionurl="https://web.archive.org/web/20250508165642/https://press.umich.edu/For-Authors/Author-s-Guide" data-versiondate="2025-05-08" target="_blank">U-M Press’s Author’s Guide</a>  documentation includes a definition of alt text, as well as links to the <a href="https://describingvisualresources.org/" data-versionurl="https://web.archive.org/web/20250430080916/https://describingvisualresources.org/" data-versiondate="2025-04-30" target="_blank">Describing Visual Resources Toolkit</a> and <a href="https://press.umich.edu/content/download/136831/1887235/version/1/file/SampleTextualDescriptionsforIllustrativeMaterials-2019.pdf" data-versionurl="https://web.archive.org/web/20250307212515/https://press.umich.edu/content/download/136831/1887235/version/1/file/SampleTextualDescriptionsforIllustrativeMaterials-2019.pdf" data-versiondate="2025-03-07" target="_blank">Sample Textual Descriptions for Illustrative Materials</a>.
[/example]
