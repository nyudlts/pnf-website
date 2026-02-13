---
title: '62. For a custom web application built for a single publication that requires a web server to run, work with the developers and authors to prepare a clean self-contained, installation package for the purpose of backup and preservation.'
taxonomy:
    tag:
        - planning
        - 'web-based publications'
---

When other methods of preserving a web publication (export, web crawling) cannot appropriately capture the important properties of a publication because it is dynamic and data-driven, a preservation service may attempt to preserve the application itself with the goal of running it in an emulated web server environment in the future. In order to do this, the preservation service would require a clean installation package as well as documentation of the requirements, dependencies, and installation process. A preservation copy could be created during the publication process. Work with the developer and author to ensure this preservation copy: functions fully in a self-contained web server that does not have access to any resources outside of the machine; does not contain any server information or logs; uses relative links that do not contain a specific domain name; and contains only local stylesheet, font, or JavaScript references. Where features require a live third-party site, consider a local functionality that could replace it adequately in this package. Overall, it would be beneficial for the developers of the publication to design any website with sustainability and encapsulation in mind—ensuring files are local to the application where possible and that there is a simple way to fallback to local functionality for integrations such as third-party resources.

_These guidelines also discuss the installation package for a web application:_  

* _[58 - Consider encapsulation of custom-built web applications early](/guidelines/58-web-application-encapsulation)_  
* _[60 - Request an installation script for custom software and websites](/guidelines/60-request-installation-script)_  
* _[61 - Produce packages for software and websites that don’t require a live server](/guidelines/61-clean-html-package)_
