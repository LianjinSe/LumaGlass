# Playfair Display

Original Google Fonts Latin WOFF2 subsets, version 40 of the hosted assets.
The font binaries are distributed without modification under SIL OFL 1.1;
see `OFL.txt` for the original copyright and Reserved Font Name notice.

- Project: https://github.com/clauseggers/Playfair-Display
- Google Fonts CSS: https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap
- Upright source: https://fonts.gstatic.com/s/playfairdisplay/v40/nuFiD-vYSZviVYUb_rj3ij__anPXDTzYgA.woff2
- Italic source: https://fonts.gstatic.com/s/playfairdisplay/v40/nuFkD-vYSZviVYUb_rj3ij__anPXDTnogkk7.woff2
- License: https://github.com/google/fonts/blob/main/ofl/playfairdisplay/OFL.txt

Both files support the variable weight axis from 400 through 900 and together
contain 77,208 bytes. Content hashes in filenames prevent stale font caches.
The stylesheet retains the original Latin Unicode range; other scripts use
the configured interface font. No Google Fonts request is made by visitors.

The CSS alias `Playfair Display Italic` uses the original italic binary at
weight 400 while keeping CSS `font-style: normal`. This lets display labels
use italic Latin glyphs without slanting or changing the weight of Chinese
fallback glyphs. The alias does not modify or rename the font binaries.

The stylesheet is included on all pages when the theme setting `homeLatinFont`
is `playfair-display`. Shared header and footer branding uses the italic face
on every route. Homepage lists and the About page (slug `about`) additionally
use Playfair Display for their Latin text; the About introduction inherits
this pairing. Article bodies and other page content keep their existing fonts.
The browser loads only the font faces used by the current page.
