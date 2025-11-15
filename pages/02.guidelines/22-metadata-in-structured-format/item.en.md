---
title: '22. Express metadata in a structured format that is appropriate for the metadata.'
taxonomy:
    tag:
        - 'export packages'
---

When exporting metadata, ensure that the data format used to express it is appropriate for the content. For example, a CSV file will work for very simple metadata, but if the fields contain formatting, values that include new lines, or express specific data types, a CSV export could become unreliable or difficult to process. A structured format such as JSON or XML is generally more appropriate and can be validated for errors more easily.