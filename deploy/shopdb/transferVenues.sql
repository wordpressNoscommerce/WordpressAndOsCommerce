truncate wordpress.wp_gigpress_venues;

insert into wordpress.wp_gigpress_venues

-- w.`venue_id`, w.`venue_name`, w.`venue_address`, w.`venue_city`,
-- w.`venue_country`, w.`venue_url`, w.`venue_phone`

SELECT distinct
      null venue_id, d.date_location venue_name, null venue_address, d.date_city venue_city,
      c.countries_iso_code_2 venue_country, d.date_link venue_url, null venue_phone
FROM wordpress.dates d
JOIN wordpress.countries c on d.countries_id = c.countries_id
group by d.`date_city`, d.`date_location`
order by d.date_location;
