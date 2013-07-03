-- drop triggers

DROP TRIGGER "5000__application_log_clear_old_log" ON "application_log" CASCADE;
DROP FUNCTION "application_log_clear_old_log"() CASCADE;

-- drop tables

DROP TABLE "application_log_property" CASCADE;
DROP TABLE "application_log" CASCADE;
