Custom Carousel Plugin
A premium WordPress plugin to create and manage multiple responsive carousels with customizable dimensions, gap sizes, gap colors, visible items, and animation speeds. Display images or videos using shortcodes on your WordPress site.
 
Features

Multiple Carousels: Create unlimited carousels with unique settings.
Media Support: Add images and videos to carousels using the WordPress media library.
Customizable Settings:
Set dimensions (e.g., 500x800).
Adjust number of visible items (1–10).
Control animation speed (100–5000 ms).
Customize gap size (0–100 px) and gap color.


Shortcode Integration: Embed carousels anywhere using shortcodes like [ccp3o1_custom_carousel id="carousel_12345"].
Responsive Design: Powered by Slick Slider for smooth, mobile-friendly carousels.
Admin Interface: User-friendly dashboard to add, edit, and delete carousels.
Premium Support: Support the developer via Buy Me a Coffee.

Installation

Download the Plugin:

Clone this repository or download the ZIP file from GitHub.

git clone https://github.com/3rroronly1/custom-carousel-plugin.git


Verify Slick Slider Files:

Ensure the assets/slick/ directory contains slick.css, slick-theme.css, and slick.min.js. These files are included in the repository.
If carousels don’t slide or display incorrectly, re-download Slick Slider from https://kenwheeler.github.io/slick/ and replace the files in assets/slick/.


Upload to WordPress:

Copy the custom-carousel-plugin folder to your WordPress site’s wp-content/plugins/ directory.
Alternatively, upload the ZIP file via Plugins > Add New > Upload Plugin in the WordPress admin panel (see Distribute as ZIP below).


Activate the Plugin:

Go to Plugins > Installed Plugins in WordPress and activate Custom Carousel Plugin.


Verify Setup:

Confirm the assets/slick/ directory contains the required Slick Slider files.
Check that the plugin menu appears under Custom Carousel in the WordPress admin sidebar.



Usage

Create a Carousel:

Navigate to Custom Carousel in the WordPress admin menu.
Under Add New Carousel, configure:
Media: Select images/videos using the "Add Media" button.
Dimensions: Enter width x height (e.g., 500x800).
Visible Items: Set how many items display at once (1–10).
Speed: Set animation speed in milliseconds (e.g., 300).
Gap: Set gap size in pixels (e.g., 10).
Gap Color: Choose a hex color (e.g., #ffffff).


Click Save Carousel.


Embed the Carousel:

Copy the shortcode from the Existing Carousels section (e.g., [ccp3o1_custom_carousel id="carousel_68964d3ce32c3"]).
Paste it into a page, post, or widget using the WordPress editor (e.g., Gutenberg or Classic Editor).


Delete a Carousel:

In the Existing Carousels section, click Delete Carousel next to the carousel you want to remove.
Confirm the carousel is removed from the list and its shortcode no longer works.


Reset Carousels (if needed):

Use the Reset All Carousels button in the admin page to clear all carousels if issues occur.


Create a ZIP File:

Navigate to the custom-carousel-plugin folder on your computer.
Ensure all required files are included:
custom-carousel-plugin.php
assets/css/ccp3o1-styles.css
assets/css/ccp3o1-admin.css
assets/js/ccp3o1-scripts.js
assets/js/ccp3o1-admin.js
assets/slick/slick.css
assets/slick/slick-theme.css
assets/slick/slick.min.js
readme.md (optional for distribution)


On Windows:
Right-click the custom-carousel-plugin folder, select Send to > Compressed (zipped) folder.
Name the file custom-carousel-plugin.zip.


On macOS/Linux:
Run in terminal:cd /path/to/custom-carousel-plugin
zip -r custom-carousel-plugin.zip .




Verify the ZIP contains the folder structure with all files.


Upload to WordPress:

Log in to your WordPress admin panel (e.g., peru-wolf-624893.hostingersite.com/wp-admin).
Go to Plugins > Add New > Upload Plugin.
Click Choose File, select custom-carousel-plugin.zip, and click Install Now.
Activate the plugin after installation.

Directory Structure
custom-carousel-plugin/
├── assets/
│   ├── css/
│   │   ├── ccp3o1-styles.css
│   │   ├── ccp3o1-admin.css
│   ├── js/
│   │   ├── ccp3o1-scripts.js
│   │   ├── ccp3o1-admin.js
│   ├── slick/
│   │   ├── slick.css
│   │   ├── slick-theme.css
│   │   ├── slick.min.js
├── custom-carousel-plugin.php
├── readme.md

Requirements

WordPress: Version 5.0 or higher (tested up to 6.6+).
PHP: Version 7.4 or higher.
Memory Limit: At least 256M.
Slick Slider: Version 1.8.1 (included in assets/slick/). Update from Slick Slider if issues arise.

Contributing
Contributions are welcome! To contribute:

Fork this repository.
Create a new branch (git checkout -b feature/your-feature).
Commit your changes (git commit -m "Add your feature").
Push to the branch (git push origin feature/your-feature).
Open a pull request.

Please ensure your code follows WordPress coding standards and includes appropriate documentation.
Support the Developer
Love this plugin? Support ongoing development by buying me a coffee!

License
This plugin is licensed under the GPLv2 or later.
Credits

Author: 3rrorOnly1
Slick Slider: Ken Wheeler (MIT License)
Built with ❤️ for the WordPress community.
