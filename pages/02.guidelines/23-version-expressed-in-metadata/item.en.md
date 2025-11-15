---
title: '23. Ensure any bibliographic metadata associated with a publication includes an expression of versioning.'
taxonomy:
    tag:
        - 'publishing platforms'
        - 'export packages'
---

Current publishing platforms can support frequent updates and new versions. These should be expressed clearly through the metadata so that the preserved copies can be properly distinguished from each other. If something has changed, it should be reflected in the version and date and where necessary, new exports should be provided.

_These guidelines also relate to versioning:_  
_9. [Determine the version of record in you context](/guidelines/9-define-version-of-record)_  
_31. [Assign new identifiers to significant versions of a work](/guidelines/31-persistent-identifiers-for-versions)_

!> Example
 Fulcrum has structured its export packages, which include EPUBs, to support preservation. The enhanced media viewers that are used in the online version of Fulcrum EPUBs will not work if the Fulcrum platform is no longer available. To help ensure the EPUBs will continue to have essential functionality over the long term, the export process simplifies these features. For photos, it embeds a static view of the photo inside the EPUB instead of depending on a IIIF viewer. For audio and video, it displays a DOI link to the media resource instead of retaining the enhanced media players for these features, since these will not work if Fulcrum is unavailable. Where the players were once embedded in the EPUB, instead a persistent DOI link is displayed to point to the current location of that resource. The export package also includes all media files, as well as a CSV registry that indicates which DOI points to which file, so that the linked file can be identified even if the DOI does not resolve. These features are all applied in a way that conforms to the EPUB 3 standard.
 !@
