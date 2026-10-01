PDF.js viewer note
This widget uses the browser-native iframe PDF viewer by default (no extra JS).
Optional custom toolbar in ltxe-pdf-embed.js reloads the iframe with PDF open parameters (#page and #zoom).
Full PDF.js 4.x (Apache-2.0, https://mozilla.github.io/pdf.js/) is not bundled here to avoid shipping a large unused worker when native viewing is sufficient.
Sample demo URL: https://mozilla.github.io/pdf.js/web/compressed.tracemonkey-pldi-09.pdf
If a future release embeds pdf.min.js + pdf.worker.min.js, add those files here with SPDX Apache-2.0.
