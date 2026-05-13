# Trace Travel WordPress Theme

Custom WordPress child theme for the Trace Travel booking website. The theme extends Flatsome and customizes WooCommerce product, cart, checkout, email, booking, gallery, and homepage behavior for private car tours and transfer services.

## Requirements

- WordPress
- Flatsome parent theme
- WooCommerce
- Advanced Custom Fields / ACF Pro
- Contact Form 7
- Rank Math SEO, optional but supported for breadcrumbs

## Features

- Custom WooCommerce single product tour page with hero image, gallery, trip details, tabs, FAQs, reviews, and booking controls.
- AJAX booking flow for loading services, loading trip data, and adding trips to the cart.
- Custom booking metadata for departure date, departure time, add-ons, holiday fees, and nighttime fees.
- WooCommerce cart, checkout, mini-cart, and email template overrides.
- Homepage shortcodes for featured products, galleries, booking form, customer reviews, and social links.
- Conditional CSS and JavaScript loading for homepage, product, archive, cart, checkout, contact, and post pages.
- Contact Form 7 layout adjustments and disabled automatic paragraph wrapping.

## Installation

1. Install and activate the Flatsome parent theme.
2. Install and activate the required plugins listed above.
3. Copy this repository to:

   ```text
   wp-content/themes/trace-travel
   ```

4. In WordPress Admin, go to `Appearance > Themes` and activate `Flatsome Child`.
5. Configure WooCommerce products, product variations, product categories, ACF fields, and ACF option pages used by the theme.

## Project Structure

```text
api/                         AJAX handlers for booking and trip data
css/                         Theme styles loaded conditionally
js/                          Theme scripts loaded conditionally
shortcodes/                  Custom shortcode implementations
template-parts/              Post template overrides
woocommerce/                 WooCommerce template overrides
functions.php                Theme bootstrap, includes, filters, and hooks
single-product.php           Custom WooCommerce tour product template
style.css                    Child theme metadata and global styles
```

## Shortcodes

The theme registers these shortcodes:

- `[booking_form]`
- `[customer_reviews]`
- `[gallery_in_homepage]`
- `[gallery_short_code]`
- `[homepage_featured_products]`
- `[img_by_id]`
- `[link_to]`
- `[show_checkout_when_cart_had_item]`
- `[socials_media]`

## AJAX Actions

The booking flow uses WordPress AJAX actions:

- `get_services`
- `get_trip_data`
- `handle_booking`

The frontend receives `admin-ajax.php` and nonce data through the localized `api_data` JavaScript object.

## ACF Data

Several templates expect ACF fields or option fields, including:

- Product fields: `trip_info`, `video`, `faqs`, `add_on`
- Option fields: `holidays`, `feedbacks`, `homepage_gallery`, `socials`

Make sure these fields exist before deploying to a new environment.

## WooCommerce Notes

This theme overrides WooCommerce templates in the `woocommerce/` directory. After updating WooCommerce, compare these files against the latest plugin templates and update them if needed to avoid compatibility issues.

Booking totals are customized through cart item data and cart fees. Test product variations, add-ons, holiday fee rules, nighttime fees, cart display, checkout display, and order emails after any booking-related change.

## Development

There is no build step in this repository. PHP, CSS, and JavaScript files are edited directly.

Useful checks before publishing changes:

```bash
php -l functions.php
php -l single-product.php
find api shortcodes woocommerce template-parts -name "*.php" -print0 | xargs -0 -n1 php -l
```

## Deployment

Deploy the theme folder to the WordPress themes directory on the target server. Do not commit environment-specific credentials, local SFTP settings, `.env` files, or files in `not-upload/`.

## License

This project is licensed under the GNU General Public License v2. See [LICENSE](LICENSE).
