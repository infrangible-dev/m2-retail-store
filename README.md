# Infrangible Retail Store

Store locator for shops with physical branches: retail stores managed in the admin, shown on Google Maps, a search for
the nearest store and a page for every store. The pages and search results are CMS blocks, so their layout can be
changed without a theme. Uses [Infrangible Google Maps](https://www.infrangible.dev/modules/google-maps) for the maps and
the Geocoding API, and [Infrangible CMS Filter](https://www.infrangible.dev/modules/cms-filter) for the template syntax.

## Admin

Infrangible > Retail Store:

- Manage Retail Stores: code, name, description, image, opening hours, special opening hours, status, contact data,
  address, directions, latitude and longitude, map image, template (CMS block), URL key, SEO text, note with type and
  features
- Manage Features: name, image and status of features like parking, assigned to stores

Images are saved in `pub/media/retail_store` (JPG, GIF, PNG or WebP).

## Store pages

Every active store has a page under its URL key with the URL suffix of categories, for example `munich.html`. The
page shows the CMS block of the store, or the CMS block of the configuration, filtered with the data of the store:

| Variable                     | Value                                                                   |
|------------------------------|-------------------------------------------------------------------------|
| `name`, `street`, `city`, …  | the attributes of the store                                             |
| `has_<attribute>`            | whether the attribute has a value, for `{{if}}`                         |
| `url`                        | the URL of the store page                                               |
| `features`                   | the features of the store (`name`, `image`)                             |
| `nearby_stores`              | the three nearest other stores, with these variables and `distance`     |

## Widgets

| Widget                          | Content                                                                                   |
|---------------------------------|-------------------------------------------------------------------------------------------|
| Retail Stores Search            | a location with suggestions of Google Maps (countries of the widget, Germany by default), a radius and the buttons to search and to determine the location |
| Retail Stores Map               | a map with all active stores as markers                                                   |
| Retail Stores Map within Radius | a map with the active stores within a distance of a center, with header, result and footer CMS blocks |

The search shows the result CMS block of the widget with the found stores, sorted by distance, or the CMS block for no
result. The result blocks get the stores as variable `retail_stores` (with `distance` in kilometers) and their markers
as variable `marker_json`, for the widget "Google Maps Json Marker":

```html
{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker" map_id="result" marker_json="#var marker_json#"}}
{{for retail_store in retail_stores}}
  <h3>{{var retail_store.name}}</h3>
  {{fif retail_store.has_opening_hours}}#trans "Opening Hours"#: {{var retail_store.opening_hours}}{{/fif}}
  <a href="{{var retail_store.url}}">#trans "Store Detail Page"#</a>
{{/for}}
```

Only active CMS blocks of all store views or the current one are shown.

## Configuration

Stores > Configuration > Infrangible Solutions > Retail Store:

- CMS Block: the template of store pages without an own template
- Info Window CMS Block: the content of the info windows of the markers, filtered with the data of the store

The API key of Google Maps is set in Infrangible Google Maps.

## Demo

```bash
bin/magento retail-store:demo:data
bin/magento retail-store:demo:content
```

The first command creates 18 stores, the second the CMS blocks, the page `retail-stores` and the configuration.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer
- An API key of the Google Maps Platform

For the Hyvä theme: [Infrangible Retail Store Hyvä](https://www.infrangible.dev/modules/retail-store-hyva). The store
pages in the XML sitemap: [Infrangible Retail Store Sitemap](https://www.infrangible.dev/modules/retail-store-sitemap).

## Installation

```bash
composer require infrangible/m2-retail-store
bin/magento module:enable Infrangible_RetailStore
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Retail Store is licensed under the MIT License - see the LICENSE file for details.
