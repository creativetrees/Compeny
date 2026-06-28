-- ─────────────────────────────────────────────────────────────────────────────
-- PostgreSQL least-privilege roles for Creative Trees.
--
-- Problem: the default Docker bootstrap role (POSTGRES_USER) is a SUPERUSER, and
-- using it for the running app means a SQLi/RCE runs with superuser rights.
--
-- Fix: a DDL/owner role used only at deploy time, and a runtime role (web + queue)
-- that can only SELECT/INSERT/UPDATE/DELETE. Run this once as a superuser against
-- the app database, then set:
--     deploy/migrations  → DB_USERNAME=ctg_migrator
--     running app + queue → DB_USERNAME=ctg_app
-- ─────────────────────────────────────────────────────────────────────────────

-- 1) Roles (neither is a superuser).
CREATE ROLE ctg_migrator LOGIN PASSWORD 'CHANGE_ME_migrator' NOSUPERUSER NOCREATEDB NOCREATEROLE;
CREATE ROLE ctg_app      LOGIN PASSWORD 'CHANGE_ME_app'      NOSUPERUSER NOCREATEDB NOCREATEROLE;

-- 2) Lock down the public schema, then grant deliberately.
REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT USAGE          ON SCHEMA public TO ctg_app;
GRANT USAGE, CREATE  ON SCHEMA public TO ctg_migrator;
ALTER DATABASE creative_trees OWNER TO ctg_migrator;

-- 3) Runtime role: DML only on existing objects.
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES    IN SCHEMA public TO ctg_app;
GRANT USAGE, SELECT                  ON ALL SEQUENCES  IN SCHEMA public TO ctg_app;

-- 4) Future tables created by migrations inherit the app grants automatically.
ALTER DEFAULT PRIVILEGES FOR ROLE ctg_migrator IN SCHEMA public
  GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES    TO ctg_app;
ALTER DEFAULT PRIVILEGES FOR ROLE ctg_migrator IN SCHEMA public
  GRANT USAGE, SELECT                 ON SEQUENCES  TO ctg_app;

-- 5) Bound runaway queries / leaked transactions at the role level.
ALTER ROLE ctg_app SET statement_timeout = '15s';
ALTER ROLE ctg_app SET idle_in_transaction_session_timeout = '30s';
ALTER ROLE ctg_migrator SET statement_timeout = '0';   -- long DDL is legitimate

-- Foreign-key indexes are handled by the Laravel migration
-- 2026_06_28_020000_add_foreign_key_indexes.php (Postgres does not auto-index FKs).
