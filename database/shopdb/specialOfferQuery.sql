SELECT p.products_id,
       p.products_image,
       p.products_image_lrg,
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
       pd.products_format,
       pd.products_viewed,
       pd.products_head_keywords_tag,
       p.products_upc,
       p.products_isrc
FROM products p
LEFT JOIN products_description pd USING (products_id)
LEFT JOIN manufacturers m USING (manufacturers_id)
LEFT JOIN specials s USING (products_id)
LEFT JOIN products_to_categories p2c USING (products_id)
WHERE p.products_status = '1'
  AND p.products_parent = ''
  AND (lower(pd.products_head_keywords_tag) LIKE '%of 10%'
       OR lower(pd.products_head_keywords_tag) LIKE '%sale%'
       OR lower(pd.products_head_keywords_tag) LIKE '%action%'
       OR lower(pd.products_head_keywords_tag) LIKE '%size%')
ORDER BY p.products_id DESC