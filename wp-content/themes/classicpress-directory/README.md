# Bedrock

This is an accessible ClassicPress starter theme, called Bedrock. It is intended to provide a minimum of code for a working website, with the idea that you edit it as you prefer to make your own site. No need for child themes!


## Special Features

While Bedrock takes a minimalistic approach, it also comes with several special features:

- Bedrock can be configured with one, two, or no sidebars, and will automatically adjust its appearance accordingly. (You can, of course, edit the CSS if you wish to modify something.)

- Bedrock comes with normalize.css (to overcome differences in rendering by different borowsers) and new-css-reset.css (to overcome default browser styling). This leaves you free to focus on the main theme stylesheet to make any modifications you choose, without having to fight with borwosers' own stylesheets.

- **NOTE**: do **not** be tempted to concatenate stylesheets. That used to be good practice but is true no longer. These days it is better to treat stylesheets as small modules, which can then all be loaded by the browser concurrently.

- Bedrock uses CSS custom properties (located at the top of the main theme stylesheet), so that it is possible to change fonts and colors in many places just by making one change in the custom properties.

- Bedrock comes with a built-in dark mode (located just below the CSS custom properties in the main theme stylesheet). Users who have set their devices to prefer dark mode will automatically be served the dark mode CSS. If you do not wish to make use of this feature, simply comment out or delete the dark mode CSS.

- Bedrock comes with three menus, two of which may be filled from within your site. The top menu is designed for static links, such as to social media, and will need to be edited from within the header.php template. You are, of course, free to add, delete, or modify any of these menus.
