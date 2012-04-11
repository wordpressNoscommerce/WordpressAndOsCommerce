SELECT p.products_id,
       p.products_tax_class_id,
       p.products_parent products_model,
       pd.products_format,
       IF(s.status, s.specials_new_products_price, p.products_price) AS products_price
FROM   products p
       LEFT JOIN specials s ON p.products_id = s.products_id
       LEFT JOIN products_description pd ON p.products_id = pd.products_id
WHERE  p.products_status = '1'
       AND p.products_parent = 'strike0129'
       AND p.products_prelisten NOT LIKE '%.mp3%'
       AND pd.products_format NOT LIKE '%MP3-Single%'
       AND pd.language_id = '2'
ORDER  BY pd.products_format ASC
