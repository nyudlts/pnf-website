---
title: '67. When software is included in an archive package, provide both the compiled software and the source code if available.'
taxonomy:
    tag:
        - 'software and data'
---

Compiled software may be opaque or impossible to modify, while source code may be impossible to compile if build dependencies become unavailable. Supplying both can enable different preservation pathways. If compiled software can no longer run due to an incompatible operating system, it may be possible to match it with an appropriate emulator. Source code provides future users with an opportunity to understand what the software is if the documentation is insufficient and may also allow modifications to the software to work in a different environment or context. Ensure that the software license is expressed within the package and appropriate for reuse.

_These guidelines refer to the creation of the installation package:_  
_60. [Request an installation script for custom software](/guidelines/60-request-installation-script)_  
_61. [Create installation packages for custom websites that don’t require a live server](/guidelines/61-clean-html-package)_  
_62. [Create installation packages for custom websites that do require a live server](/guidelines/62-clean-install-dynamic-web)_