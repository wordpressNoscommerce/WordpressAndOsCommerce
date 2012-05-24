
			SELECT	p.products_id,
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
			LEFT JOIN products_description pd ON pd.products_id = p.products_id
			LEFT JOIN manufacturers m ON m.manufacturers_id = p.manufacturers_id
			LEFT JOIN specials s ON p.products_id = s.products_id
			WHERE p.products_id = 4376
			UNION
			SELECT	parent.products_id,
				parent.products_image,
				parent.products_image_lrg,
				pd.products_name,
				parent.products_model,
				parent.products_weight,
				parent.products_quantity,
				parent.products_price,
				parent.manufacturers_id,
				m.manufacturers_name,
				m.manufacturers_image,
				parent.products_tax_class_id,
				IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
				IF(s.status, s.specials_new_products_price, parent.products_price) AS final_price,
				pd.products_description,
				pd.products_format,
				pd.products_viewed,
				pd.products_head_keywords_tag,
				parent.products_upc,
				p.products_isrc
			FROM products p
		  LEFT JOIN products parent on parent.products_model = p.products_parent
			LEFT JOIN products_description pd ON pd.products_id = parent.products_id
			LEFT JOIN manufacturers m ON m.manufacturers_id = parent.manufacturers_id
			LEFT JOIN specials s ON parent.products_id = s.products_id
			WHERE p.products_id = 4376