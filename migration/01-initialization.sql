-- ----------------------------
-- Table structure for metrics
-- ----------------------------
DROP TABLE IF EXISTS "public"."metrics";
CREATE TABLE "public"."metrics" (
  "id" int4 NOT NULL,
  "blockchain" varchar(255) COLLATE "pg_catalog"."default",
  "total_capitalization" varchar(255) COLLATE "pg_catalog"."default",
  "shielded_pool_capitalization" varchar(255) COLLATE "pg_catalog"."default",
  "shielded_percentage" varchar(255) COLLATE "pg_catalog"."default",
  "calculated_at" timestamp(6)
)
;

-- ----------------------------
-- Table structure for monero_blocks
-- ----------------------------
DROP TABLE IF EXISTS "public"."monero_blocks";
CREATE TABLE "public"."monero_blocks" (
  "height" int8 NOT NULL,
  "hash" varchar(128) COLLATE "pg_catalog"."default",
  "miner_tx_hash" varchar(128) COLLATE "pg_catalog"."default",
  "difficulty" int8,
  "size" int4,
  "timestamp" int4,
  "transactions" int4,
  "major_version" int2,
  "minor_version" int2,
  "block" jsonb,
  "created_at" timestamp(6),
  "updated_at" timestamp(6)
)
;
COMMENT ON COLUMN "public"."monero_blocks"."height" IS 'block';
COMMENT ON COLUMN "public"."monero_blocks"."timestamp" IS 'epoch & unix timestamp';
COMMENT ON COLUMN "public"."monero_blocks"."transactions" IS 'number of transactions';

-- ----------------------------
-- Table structure for monero_transactions
-- ----------------------------
DROP TABLE IF EXISTS "public"."monero_transactions";
CREATE TABLE "public"."monero_transactions" (
  "height_id" int8,
  "tx_hash" varchar(128) COLLATE "pg_catalog"."default" UNIQUE,
  "transaction" jsonb,
  "created_at" timestamp(6),
  "updated_at" timestamp(6)
)
;
COMMENT ON COLUMN "public"."monero_transactions"."height_id" IS 'block height';

-- ----------------------------
-- Primary Key structure for table metrics
-- ----------------------------
ALTER TABLE "public"."metrics" ADD CONSTRAINT "metrics_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table monero_blocks
-- ----------------------------
ALTER TABLE "public"."monero_blocks" ADD CONSTRAINT "monero_blocks_pkey" PRIMARY KEY ("height");

-- ----------------------------
-- Foreign Keys structure for table monero_transactions
-- ----------------------------
ALTER TABLE "public"."monero_transactions" ADD CONSTRAINT "monero_transactions_height_id_fkey" FOREIGN KEY ("height_id") REFERENCES "public"."monero_blocks" ("height") ON DELETE NO ACTION ON UPDATE NO ACTION;

-- ----------------------------
-- Table structure for zcash_blocks
-- ----------------------------
DROP TABLE IF EXISTS "public"."zcash_blocks";
CREATE TABLE "public"."zcash_blocks" (
  "height" int8 NOT NULL,
  "hash" varchar(128) COLLATE "pg_catalog"."default",
  "size" int4,
  "time" int4,
  "transactions" int4,
  "version" int2,
  "block" jsonb,
  "created_at" timestamp(6),
  "updated_at" timestamp(6)
)
;
COMMENT ON COLUMN "public"."zcash_blocks"."height" IS 'block';
COMMENT ON COLUMN "public"."zcash_blocks"."time" IS 'epoch & unix timestamp';
COMMENT ON COLUMN "public"."zcash_blocks"."transactions" IS 'number of transactions';

-- ----------------------------
-- Table structure for zcash_transactions
-- ----------------------------
DROP TABLE IF EXISTS "public"."zcash_transactions";
CREATE TABLE "public"."zcash_transactions" (
  "height_id" int8,
  "txid" varchar(128) COLLATE "pg_catalog"."default" UNIQUE,
  "transaction" jsonb,
  "created_at" timestamp(6),
  "updated_at" timestamp(6)
)
;
COMMENT ON COLUMN "public"."zcash_transactions"."height_id" IS 'block height';

-- ----------------------------
-- Primary Key structure for table zcash_blocks
-- ----------------------------
ALTER TABLE "public"."zcash_blocks" ADD CONSTRAINT "zcash_blocks_pkey" PRIMARY KEY ("height");

-- ----------------------------
-- Foreign Keys structure for table zcash_transactions
-- ----------------------------
ALTER TABLE "public"."zcash_transactions" ADD CONSTRAINT "zcash_transactions_height_id_fkey" FOREIGN KEY ("height_id") REFERENCES "public"."zcash_blocks" ("height") ON DELETE NO ACTION ON UPDATE NO ACTION;
