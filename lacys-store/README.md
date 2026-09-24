# Lacy's Store — WooCommerce Theme

An elegant, fully responsive **white & red** WordPress theme for **Lacy's Store**, a women's apparel boutique based in Los Angeles. It is built for **WooCommerce** and sets up its own pages, menu, shipping and sample products on activation, so you can go from install to a working store in a few minutes.

> **Status:** initial release. It has been code-reviewed but should be tested on a staging site (register, add to cart, checkout, contact form) before going live.

---

## Features

- Custom classic theme with no page builder or extra plugin required (WooCommerce only)
- White and red palette, Playfair Display headings and Jost body text (Google Fonts)
- Fully responsive: 4-column product grid on desktop, 3 on tablet, 2 on phone, with a slide-down mobile menu
- Home page with hero, category tiles, new arrivals, sale banner, best sellers and testimonials
- Styled WooCommerce shop, product, cart, checkout and My Account pages
- Login and registration on My Account, guest checkout, live bag count in the header
- Contact form with spam trap, plus a newsletter box in the footer
- Privacy Policy, Terms & Conditions and Sitemap pages (created automatically)
- Facebook and Instagram icons, and a Los Angeles store address, all editable from the Customizer
- Payment-gateway agnostic: add Stripe, PayPal or any WooCommerce gateway later

## Pages included (10)

| # | Page | Source |
|---|------|--------|
| 1 | Home | `front-page.php` |
| 2 | Shop | WooCommerce archive (`woocommerce.php`) |
| 3 | Product | WooCommerce single product (`woocommerce.php`) |
| 4 | Cart | WooCommerce shortcode |
| 5 | Checkout | WooCommerce shortcode |
| 6 | My Account (login / register) | WooCommerce shortcode |
| 7 | About Us | Created on activation |
| 8 | Lookbook | Created on activation |
| 9 | FAQ & Shipping | Created on activation |
| 10 | Contact | Created on activation |

Also created: **Privacy Policy**, **Terms & Conditions** and **Sitemap**.

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer
- [WooCommerce](https://wordpress.org/plugins/woocommerce/) plugin (latest version recommended)

## Installation

### Option A: upload the zip (recommended)

1. Install and activate the **WooCommerce** plugin (Plugins → Add New).
2. Zip the `lacys-store` folder (or use the provided `lacys-store.zip`).
3. Go to **Appearance → Themes → Add New → Upload Theme**, choose the zip and click **Activate**.
4. Open any page in the WordPress admin once. The one-time setup runs automatically (see below).

### Option B: from GitHub

```bash
git clone https://github.com/<your-username>/<your-repo>.git
cp -r <your-repo>/lacys-store /path/to/wordpress/wp-content/themes/
```

Then activate **Lacy's Store** under Appearance → Themes.

### Option C: run it locally with Docker

Save this as `docker-compose.yml` next to the `lacys-store` folder:

```yaml
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: wp
      MYSQL_USER: wp
      MYSQL_PASSWORD: wp
      MYSQL_ROOT_PASSWORD: root
    volumes: [db:/var/lib/mysql]
  wordpress:
    image: wordpress:latest
    depends_on: [db]
    ports: ["8080:80"]
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: wp
      WORDPRESS_DB_PASSWORD: wp
      WORDPRESS_DB_NAME: wp
    volumes:
      - wp:/var/www/html
      - ./lacys-store:/var/www/html/wp-content/themes/lacys-store
volumes:
  db:
  wp:
```

```bash
docker compose up -d
```

Open <http://localhost:8080>, finish the WordPress install, install and activate WooCommerce, then activate the theme. (Tools such as LocalWP work equally well.)

## What happens on activation

The first time an admin page loads after activation, the theme:

- Creates the Home, About, Lookbook, FAQ & Shipping, Contact, Privacy Policy, Terms & Conditions and Sitemap pages, and sets Home as the front page
- Builds the **Primary** menu and assigns it
- Sets permalinks to `/%postname%/`
- Adds 8 sample products in 4 categories (Dresses, Tops, Skirts, Outerwear, Accessories)
- Configures WooCommerce so it works out of the box:
  - Enables registration on My Account and guest checkout
  - Switches Cart and Checkout to the classic shortcode versions, which every payment gateway supports
  - Adds a **$5 flat-rate** shipping method and **free shipping over $75**
  - Enables **Cash on Delivery** as a placeholder payment method
  - Links the Terms page (checkout agreement checkbox) and the Privacy Policy page

Each step runs once only. To run a step again, delete its option (for example `wp option delete lacys_wc`) or remove the option in the database: `lacys_setup`, `lacys_wc`, `lacys_legal`, `lacys_products`.

## Configuration

| Task | Where |
|------|-------|
| Store address, phone, Facebook and Instagram links | Appearance → Customize → **Store Details** |
| Add real payment gateways (Stripe, PayPal, etc.) | WooCommerce → Settings → **Payments** |
| Turn off the Cash on Delivery placeholder | WooCommerce → Settings → Payments |
| Currency, shipping rates and zones | WooCommerce → Settings → General / Shipping |
| Product photos, prices, sizes | Products → Edit |
| Edit About, Lookbook, FAQ, Contact, Privacy, Terms | Pages → Edit |
| Change the menu | Appearance → Menus |
| Free-shipping banner text and opening hours | `header.php` and `footer.php` |

### Payments

The theme does not touch payment processing. Checkout is standard WooCommerce, so any gateway plugin the client chooses will work: install it, enable it under WooCommerce → Settings → Payments, and add its API keys.

### Changing colours and fonts

All colours are CSS variables at the top of `style.css`:

```css
:root{--red:#c8102e;--red-d:#9b0c23;--blush:#fdf3f4;--ink:#1f1a1b;--mute:#6f6467;--line:#eadfe1;--r:14px}
```

Fonts are loaded in `functions.php` (`wp_enqueue_scripts`).

## Project structure

```
lacys-store/
├── style.css        Theme header + all styles (responsive)
├── functions.php    Theme setup, WooCommerce config, forms, shortcodes, Customizer, one-time setup
├── header.php       Announcement bar, logo, menu, search, account and bag
├── footer.php       Newsletter, links, address, social icons, legal links
├── front-page.php   Home page sections
├── page.php         Default page template (also used for Cart, Checkout, My Account)
├── woocommerce.php  Wrapper for Shop, categories, search and single product
├── index.php        Blog and search fallback
└── main.js          Mobile menu and live bag count
```

Shortcodes provided: `[lacys_contact]` (contact form), `[lacys_info]` (address, hours and social icons), `[lacys_sitemap]` (site map).

## Troubleshooting

- **Menu, pages or links show 404:** go to Settings → Permalinks and click **Save Changes**.
- **Contact and order emails never arrive:** many hosts block PHP mail. Install an SMTP plugin such as WP Mail SMTP and send a test email.
- **Checkout says no shipping options or payment methods:** check WooCommerce → Settings → Shipping and Payments. The theme adds defaults only if none exist.
- **Menu is empty:** assign a menu to **Primary Menu** under Appearance → Menus → Manage Locations.
- **Products show a grey placeholder image:** the sample products have no photos. Add images to each product.

## Before going live

- Replace the placeholder address, phone number and social links (Customizer → Store Details)
- Add real product photos, and swap the Lookbook colour tiles for photography
- Have a lawyer review the Privacy Policy and Terms & Conditions. They are templates, not legal advice
- Make sure the returns window (30 days) and free-shipping threshold ($75) match store policy
- Set up SMTP email and a real payment gateway, then do a full test order on staging

## License

Add a `LICENSE` file before publishing. WordPress themes are normally released under GPL-2.0-or-later.
