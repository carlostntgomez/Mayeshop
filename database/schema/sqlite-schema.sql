CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "featured_products_sections"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "subtitle" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "sub_banners_data" text,
  "product_grids_data" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "homepage_sub_banners"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "subtitle" varchar,
  "image_path" varchar not null,
  "link_url" varchar not null,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "background_color" varchar
);
CREATE TABLE IF NOT EXISTS "color_product"(
  "color_id" integer not null,
  "product_id" integer not null,
  foreign key("color_id") references "colors"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade,
  primary key("color_id", "product_id")
);
CREATE TABLE IF NOT EXISTS "colors"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "hex_code" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "colors_name_unique" on "colors"("name");
CREATE UNIQUE INDEX "colors_hex_code_unique" on "colors"("hex_code");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "tags"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "type" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "tags_slug_type_unique" on "tags"("slug", "type");
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text,
  "type" varchar not null default 'text',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "settings_key_unique" on "settings"("key");
CREATE TABLE IF NOT EXISTS "product_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "gender" varchar,
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" varchar,
  "image" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "product_types_name_unique" on "product_types"("name");
CREATE UNIQUE INDEX "product_types_slug_unique" on "product_types"("slug");
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "image" varchar,
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" varchar,
  "category_type_id" integer,
  "parent_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_type_id") references "product_types"("id") on delete cascade,
  foreign key("parent_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "products"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "short_description" varchar,
  "price" numeric not null default '0',
  "sale_price" numeric,
  "stock" integer not null default '0',
  "low_stock_threshold" integer not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "status" varchar not null default 'draft',
  "main_image" varchar,
  "image_gallery" text,
  "compare_price" numeric,
  "cost" numeric,
  "sku" varchar,
  "barcode" varchar,
  "quantity" integer not null default '0',
  "security_stock" integer not null default '0',
  "is_visible" tinyint(1) not null default '0',
  "seo_title" varchar,
  "seo_description" text,
  "seo_keywords" varchar,
  "weight" numeric,
  "weight_unit" varchar,
  "length" numeric,
  "width" numeric,
  "height" numeric,
  "dimension_unit" varchar,
  "average_rating" numeric,
  "is_most_selling" tinyint(1) not null default '0',
  "product_type_id" integer,
  "category_id" integer,
  "brand_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_type_id") references "product_types"("id") on delete set null,
  foreign key("category_id") references "categories"("id") on delete set null,
  foreign key("brand_id") references "brands"("id") on delete set null
);
CREATE UNIQUE INDEX "products_slug_unique" on "products"("slug");
CREATE UNIQUE INDEX "products_sku_unique" on "products"("sku");
CREATE UNIQUE INDEX "products_barcode_unique" on "products"("barcode");
CREATE TABLE IF NOT EXISTS "brands"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "brands_name_unique" on "brands"("name");
CREATE UNIQUE INDEX "brands_slug_unique" on "brands"("slug");
CREATE TABLE IF NOT EXISTS "product_tag"(
  "product_id" integer not null,
  "tag_id" integer not null,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("tag_id") references "tags"("id") on delete cascade,
  primary key("product_id", "tag_id")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "banners"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "subtitle" varchar,
  "price_text" varchar,
  "button_text" varchar,
  "button_url" varchar,
  "is_active" tinyint(1) not null default '1',
  "order" integer not null default '0',
  "image_path" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "videos"(
  "id" integer primary key autoincrement not null,
  "title" varchar,
  "video_url" varchar not null,
  "is_active" tinyint(1) not null default '1',
  "image_path" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "flash_sales"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "start_date" datetime not null,
  "end_date" datetime not null,
  "discount_percentage" numeric,
  "discount_amount" numeric,
  "is_active" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "flash_sale_product"(
  "flash_sale_id" integer not null,
  "product_id" integer not null,
  foreign key("flash_sale_id") references "flash_sales"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade,
  primary key("flash_sale_id", "product_id")
);
CREATE TABLE IF NOT EXISTS "reviews"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "user_id" integer,
  "reviewer_name" varchar,
  "reviewer_email" varchar,
  "reviewer_phone" varchar,
  "rating" integer not null,
  "review_text" text,
  "is_approved" tinyint(1) not null default '0',
  "is_featured" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "gallery_images"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "image_path" varchar not null,
  "alt_text" varchar,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "blog_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "blog_categories_name_unique" on "blog_categories"("name");
CREATE UNIQUE INDEX "blog_categories_slug_unique" on "blog_categories"("slug");
CREATE TABLE IF NOT EXISTS "blog_tags"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "blog_tags_name_unique" on "blog_tags"("name");
CREATE UNIQUE INDEX "blog_tags_slug_unique" on "blog_tags"("slug");
CREATE TABLE IF NOT EXISTS "posts"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "slug" varchar not null,
  "content" text not null,
  "excerpt" text,
  "featured_image" varchar,
  "published_at" datetime,
  "status" varchar not null default 'draft',
  "user_id" integer not null,
  "blog_category_id" integer,
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" varchar,
  "is_featured" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("blog_category_id") references "blog_categories"("id") on delete set null
);
CREATE UNIQUE INDEX "posts_slug_unique" on "posts"("slug");
CREATE TABLE IF NOT EXISTS "post_blog_tag"(
  "post_id" integer not null,
  "blog_tag_id" integer not null,
  foreign key("post_id") references "posts"("id") on delete cascade,
  foreign key("blog_tag_id") references "blog_tags"("id") on delete cascade,
  primary key("post_id", "blog_tag_id")
);
CREATE TABLE IF NOT EXISTS "collections"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "collections_name_unique" on "collections"("name");
CREATE UNIQUE INDEX "collections_slug_unique" on "collections"("slug");
CREATE TABLE IF NOT EXISTS "product_collection"(
  "product_id" integer not null,
  "collection_id" integer not null,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("collection_id") references "collections"("id") on delete cascade,
  primary key("product_id", "collection_id")
);
CREATE TABLE IF NOT EXISTS "color_settings"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "value" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "color_settings_name_unique" on "color_settings"("name");
CREATE TABLE IF NOT EXISTS "whats_app_subscriptions"(
  "id" integer primary key autoincrement not null,
  "phone_number" varchar not null,
  "subscribed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "whats_app_subscriptions_phone_number_unique" on "whats_app_subscriptions"(
  "phone_number"
);
CREATE TABLE IF NOT EXISTS "orders"(
  "id" integer primary key autoincrement not null,
  "customer_name" varchar not null,
  "customer_lastname" varchar not null,
  "customer_email" varchar not null,
  "customer_phone" varchar not null,
  "customer_address" varchar not null,
  "customer_city" varchar not null,
  "customer_state" varchar not null,
  "payment_method" varchar not null,
  "total_amount" numeric not null,
  "status" varchar not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  "company_name" varchar,
  "customer_country" varchar
);
CREATE TABLE IF NOT EXISTS "order_items"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "product_id" integer not null,
  "product_name" varchar not null,
  "quantity" integer not null,
  "price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "social_media_links"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "url" varchar not null,
  "icon" varchar,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "header_announcements"(
  "id" integer primary key autoincrement not null,
  "text" varchar not null,
  "icon" varchar,
  "url" varchar,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "font_settings"(
  "id" integer primary key autoincrement not null,
  "primary_font_name" varchar,
  "secondary_font_name" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "about_page_contents"(
  "id" integer primary key autoincrement not null,
  "breadcrumb_title" varchar,
  "cover_image" varchar,
  "section1_subtitle" varchar,
  "section1_title" varchar,
  "section1_paragraph" text,
  "section1_image" varchar,
  "section2_subtitle" varchar,
  "section2_title" varchar,
  "section2_paragraph" text,
  "section2_image" varchar,
  "more_about_heading_title" varchar,
  "more_about_heading_description" text,
  "point1_title" varchar,
  "point1_description" text,
  "point2_title" varchar,
  "point2_description" text,
  "point3_title" varchar,
  "point3_description" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "faqs"(
  "id" integer primary key autoincrement not null,
  "question" varchar not null,
  "answer" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "contact_page_contents"(
  "id" integer primary key autoincrement not null,
  "breadcrumb_title" varchar not null,
  "heading_title" varchar not null,
  "heading_description" text not null,
  "address" varchar not null,
  "phone" varchar not null,
  "email" varchar not null,
  "map_embed_code" text,
  "created_at" datetime,
  "updated_at" datetime
);

INSERT INTO migrations VALUES(1,'2025_10_29_203122_create_featured_products_sections_table',1);
INSERT INTO migrations VALUES(2,'2025_10_30_003007_add_review_details_to_reviews_table',1);
INSERT INTO migrations VALUES(3,'2025_10_30_003436_create_homepage_sub_banners_table',1);
INSERT INTO migrations VALUES(4,'2025_10_30_003940_add_background_color_to_homepage_sub_banners_table',1);
INSERT INTO migrations VALUES(5,'2025_10_30_164154_create_color_product_table_with_correct_fks',1);
INSERT INTO migrations VALUES(6,'2025_10_30_165458_create_colors_table',1);
INSERT INTO migrations VALUES(7,'2025_10_30_165526_create_users_table',1);
INSERT INTO migrations VALUES(8,'2025_10_30_165553_create_tags_table',1);
INSERT INTO migrations VALUES(9,'2025_10_30_165641_create_settings_table',1);
INSERT INTO migrations VALUES(10,'2025_10_30_165735_create_product_types_table',1);
INSERT INTO migrations VALUES(11,'2025_10_30_165837_create_categories_table',1);
INSERT INTO migrations VALUES(12,'2025_10_30_170052_create_products_table',1);
INSERT INTO migrations VALUES(13,'2025_10_30_171531_import_schema_from_sql_dump',1);
INSERT INTO migrations VALUES(14,'2025_10_30_172117_create_brands_table',1);
INSERT INTO migrations VALUES(15,'2025_10_30_172454_create_product_tag_table',1);
INSERT INTO migrations VALUES(16,'2025_10_30_173247_create_sessions_table',1);
INSERT INTO migrations VALUES(17,'2025_10_30_173342_create_cache_table',1);
INSERT INTO migrations VALUES(18,'2025_10_30_173445_create_banners_table',1);
INSERT INTO migrations VALUES(19,'2025_10_30_173828_create_videos_table',1);
INSERT INTO migrations VALUES(20,'2025_10_30_174143_create_flash_sales_table',1);
INSERT INTO migrations VALUES(21,'2025_10_30_174452_create_flash_sale_product_table',1);
INSERT INTO migrations VALUES(22,'2025_10_30_174613_create_reviews_table',1);
INSERT INTO migrations VALUES(23,'2025_10_30_180238_create_gallery_images_table',1);
INSERT INTO migrations VALUES(24,'2025_10_30_180523_create_blog_categories_table',1);
INSERT INTO migrations VALUES(25,'2025_10_30_180716_create_blog_tags_table',1);
INSERT INTO migrations VALUES(26,'2025_10_30_180811_create_posts_table',1);
INSERT INTO migrations VALUES(27,'2025_10_30_181009_create_post_blog_tag_table',1);
INSERT INTO migrations VALUES(28,'2025_10_30_181620_create_collections_table',1);
INSERT INTO migrations VALUES(29,'2025_10_30_192855_create_product_collection_table',1);
INSERT INTO migrations VALUES(30,'2025_10_30_202402_create_color_settings_table',1);
INSERT INTO migrations VALUES(31,'2025_11_06_011643_create_whats_app_subscriptions_table',1);
INSERT INTO migrations VALUES(32,'2025_11_06_100000_create_orders_table',1);
INSERT INTO migrations VALUES(33,'2025_11_06_100001_create_order_items_table',1);
INSERT INTO migrations VALUES(34,'2025_11_07_041825_add_company_and_country_to_orders_table',1);
INSERT INTO migrations VALUES(35,'2025_11_07_105743_create_social_media_links_table',1);
INSERT INTO migrations VALUES(36,'2025_11_07_112007_create_header_announcements_table',1);
INSERT INTO migrations VALUES(37,'2025_11_07_160909_update_font_settings_table',1);
INSERT INTO migrations VALUES(38,'2025_11_07_183232_create_about_page_contents_table',1);
INSERT INTO migrations VALUES(39,'2025_11_07_195949_create_faqs_table',1);
INSERT INTO migrations VALUES(40,'2025_11_07_201120_create_contact_page_contents_table',1);
