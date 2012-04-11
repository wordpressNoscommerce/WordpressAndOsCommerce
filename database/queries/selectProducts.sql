SELECT
	p.products_id,
	p.products_image,
	pd.products_name,
	p.products_model,
	p.products_weight,
	p.products_quantity,
	p.products_price,
	p.manufacturers_id,
	m.manufacturers_name,
	m.manufacturers_image,
	p.products_tax_class_id,
	IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
	IF(s.status, s.specials_new_products_price, p.products_price) AS final_price, 
    pd.products_description,
    pd.products_format
FROM products p
LEFT JOIN products_description pd ON pd.products_id = p.products_id
LEFT JOIN manufacturers m ON m.manufacturers_id = p.manufacturers_id
LEFT JOIN specials s ON p.products_id = s.products_id
LEFT JOIN products_to_categories p2c ON p.products_id = p2c.products_id 
WHERE p.products_status = '1' and p.products_parent = '' 
-- and p2c.categories_id = '0'  -- optional categorie selection
ORDER BY p.products_model desc;


SELECT COUNT(p.products_id) AS cnt 
FROM products p 
LEFT JOIN products_description pd ON pd.products_id = p.products_id 
LEFT JOIN manufacturers m ON m.manufacturers_id = p.manufacturers_id 
LEFT JOIN specials s ON p.products_id = s.products_id 
LEFT JOIN products_to_categories p2c ON p.products_id = p2c.products_id 
WHERE p.products_status = '1' and p.products_parent = '' 
AND pd.products_head_keywords_tag LIKE '%Vinyl%'