# Changelog

## 2.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 1.x for these.
- Requires `infrangible/m2-backend-widget` 4.0, `infrangible/m2-cms-filter` 2.0, `infrangible/m2-core` 5.0,
  `infrangible/m2-foundation` 3.0, `infrangible/m2-google-maps` 2.0 and `fewedev/base` 2.4.
- The tables are declared in `db_schema.xml` and the EAV entity is added by a data patch; the classes `InstallSchema`,
  `InstallData` and `Helper\Setup` are removed. New installations no longer get the unused table
  `retail_store_location`.
- All classes have native types, and the constructors use constructor property promotion. The constructors of the
  helpers, controllers, blocks and widgets changed.
- The store controllers implement `HttpGetActionInterface` (and the search `HttpPostActionInterface`) instead of
  extending Magento's deprecated `Action`.
- `Store::getLatitude()` and `getLongitude()` return a float or null, `getCmsBlockId()` an integer;
  `Store::addActiveFilter()` of the collection returns the collection.
- `Data::getMarkerJson()` returns the value for the parameter of a widget; the JSON of the markers is returned by
  `Data::getMarkersJson()`.
- The search form sends its data with GET, so that results can be linked; POST is still accepted.

### Fixed

- Deleting a store deleted the quotes and orders with that store; now they are kept without the store.
- The note type of a store was never saved, because it was stored as a number.
- Store pages with an own template were only found if the configuration had a template as well.
- The info windows of the markers were empty, and with content their HTML contained escaped quotes.
- Characters like curly braces and backslashes in the texts of markers broke the JSON of the markers.
- Stores loaded in lists (search results, maps) had no features.
- The nearby stores of a store page skipped the nearest other store if two stores had the same coordinates.
- Paths without the URL suffix of categories were shortened anyway, so they could show the page of another store.
- Images could be replaced by paths outside the image directory, and deleting an image could delete other files of
  the media directory. Only images (JPG, GIF, PNG and WebP) can be uploaded.
- The search showed any CMS block with the ID of the request, also inactive blocks and blocks of other store views.
- The search widget turned off the browser cache for the whole page.
- Searching with an empty or unknown location failed; the search radius "0" found no stores.
- Templates escape their output.
- The datetime values of stores had the default `0000-00-00 00:00:00`, which strict SQL modes reject.

### Added

- Widget "Retail Stores Search": the countries of the location suggestions (default Germany, as before).
- Translations in 24 languages.
- Unit tests.
- README with the admin, the variables of the templates, the widgets and the demo.

### Changed

- The widgets are named "Retail Stores …" instead of "Retails Stores …".
- The search loads the stores in one query instead of one query per store.
- The info window of the demo content shows the opening hours if the store has them.
- The module is loaded after the modules it uses.
- Checks with PHPStan level 9, the Magento coding standard and unit tests on every push.
