/* San pham Simple */
SELECT 
    p.ID as Product_ID,
    pm.meta_value as Old_tinh_trang,
    p.guid as link
FROM
	vg_posts p
INNER JOIN    
    vg_postmeta pm ON pm.post_id = p.ID
WHERE
    meta_key = '6579_default_editor'
        AND meta_value != ''