--------------------------------------------------------------------------------
-- table: application_log                                                     --
--------------------------------------------------------------------------------

CREATE TABLE "application_log"
(
    "id"            SERIAL                      NOT NULL,
    "timestamp"     TIMESTAMP WITH TIME ZONE    NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    "loggedUserId"  INTEGER,
    "priority"      INTEGER                     NOT NULL    DEFAULT 1,
    "eventType"     CHARACTER VARYING           NOT NULL,

    PRIMARY KEY ( "id" ),
    FOREIGN KEY ( "loggedUserId" )
     REFERENCES "user" ( "id" )
      ON UPDATE CASCADE
      ON DELETE SET NULL
);

--------------------------------------------------------------------------------
-- table: application_log_property                                            --
--------------------------------------------------------------------------------

CREATE TABLE "application_log_property"
(
    "logId"     INTEGER             NOT NULL,
    "name"      CHARACTER VARYING   NOT NULL,
    "value"     TEXT,

    FOREIGN KEY ( "logId" )
     REFERENCES "application_log" ( "id" )
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

--------------------------------------------------------------------------------
-- function: application_log_clear_old_log()                                  --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "application_log_clear_old_log"()
                   RETURNS TRIGGER
                       SET search_path FROM CURRENT
                  LANGUAGE plpgsql
                        AS $$
BEGIN

    DELETE FROM "application_log"
          WHERE "timestamp" < ( CURRENT_TIMESTAMP - INTERVAL '1 month' );

    RETURN NEW;

END $$;

--------------------------------------------------------------------------------
-- trigger: application_log.application_log_clear_old_log                     --
--------------------------------------------------------------------------------

CREATE TRIGGER "5000__application_log_clear_old_log"
         AFTER INSERT
            ON "application_log"
           FOR EACH STATEMENT
       EXECUTE PROCEDURE "application_log_clear_old_log"();
