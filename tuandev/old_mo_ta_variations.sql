/* San pham Variations */
SELECT 
    parent_post.ID as Product_ID,
    p.ID as Variation_ID,
    pm.meta_value as Old_tinh_trang,
    parent_post.guid as link
FROM
	vg_posts parent_post
INNER JOIN    
	vg_posts p ON parent_post.ID = p.post_parent
INNER JOIN    
    vg_postmeta pm ON p.ID = pm.post_id
WHERE
	p.post_parent != 0 AND
    pm.meta_key = '_variation_description'
        AND pm.meta_value != ''
