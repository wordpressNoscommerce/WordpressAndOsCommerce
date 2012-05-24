SELECT * FROM featured
LEFT JOIN products ON featured.products_id = products.products_id
LEFT JOIN products_description ON featured.products_id = products_description.products_id
LEFT JOIN products_to_categories ON featured.products_id = products_to_categories.products_id
LEFT JOIN manufacturers ON products.manufacturers_id = manufacturers.manufacturers_id
WHERE products.products_parent = '' AND (products_to_categories.categories_id = 32 OR products_to_categories.categories_id = '')